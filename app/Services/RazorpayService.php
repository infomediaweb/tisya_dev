<?php



namespace App\Services;

use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

use App\Models\PropertyBooking;

use App\Models\PropertyBookingPaymentRequest;

use App\Mail\BookingPaymentRequestEmail;

use Razorpay\Api\Api;

use Config;

use Mail;

use DB;

use URL;



class RazorpayService{

   
    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

   
   public function createOrder($amount, $currency = 'INR', $receipt = 'order_rcptid_11')
    {
        try {
            $order = $this->api->order->create([
                'amount' => $amount * 100, // Amount in paisa
                'currency' => $currency,
                'receipt' => $receipt,
            ]);

            return $order['id'];
        } catch (Exception $e) {
            // Log the error or handle it
            \Log::error("Razorpay Order Creation Failed: " . $e->getMessage());
            return null;
        }
    }
  

    public static function createPaymentLink($payload){

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        

        $paymentRequestInfo = PropertyBookingPaymentRequest::find(['id'=>$payload['booking_payment_request_id']])->first();

        $propertyBooking = PropertyBooking::with('home')->where('id', $paymentRequestInfo->property_booking_id)->first();

            

        $amount = $payload['amount'];

        $customer_name = $payload['name'];

        $contact_no = $payload['contact_no'];

        $email = $payload['email'];

        try {

            $payment = $api->invoice->create([

                'type' => 'link',

                'amount' => $amount * 100,

                'description' => 'Payment Request for '.$propertyBooking->home->home_name.' || booking id ['.$paymentRequestInfo->amount.']',

                'callback_url' => 'https://tisyastays.rentals.management/razorpay/webhook/callback',

                'notify' => [

                    'sms' => true,

                    'email' => false

                ],

                'customer' => [

                    'name' => $customer_name,

                    'email' => $email,

                    'contact' =>  $contact_no,

                ],

                'currency' => 'INR'

            ]);

            

            $paymentRequestInfo->payment_link_id = $payment['id'];

            $paymentRequestInfo->payment_link = $payment['short_url'];

            $paymentRequestInfo->link_status = 'Created';

            $paymentRequestInfo->save();

            

            $emailData =  array('bookingDetail'=>$propertyBooking, 'paymentRequestInfo'=>$paymentRequestInfo);

            $data = Mail::to($email)->send(new BookingPaymentRequestEmail($emailData));

            return response()->json(['status'=>true, 'message' => 'Link created successfully'], 200);

            

        }

        catch (\Exception $e) {

            dd($e->getMessage());

            return response()->json(['status'=>false, 'message' => $e->getMessage()], 400);

        }

    }



    public static function handleWebhookCallBack($payload){

        // Construct the payload manually

        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        $payloadJson = json_encode([

            'event' => 'invoice.paid',

            'payload' => [

                'invoice' => [

                    'id' => $payload['razorpay_invoice_id'],

                    'receipt' => $payload['razorpay_invoice_receipt'],

                    'status' => $payload['razorpay_invoice_status'],

                    'payment_id' => $payload['razorpay_payment_id'],

                ]

            ]

        ]);
       // $signature = $request->header('X-Razorpay-Signature');
        $webhookSecret = config('services.razorpay.webhook_secret');
        try {
            //$api->utility->verifyWebhookSignature($payloadJson, $signature, $webhookSecret);
            $paymentRequestDetail = PropertyBookingPaymentRequest::where(['payment_link_id'=>$payload['razorpay_invoice_id']])->first();
            $paymentRequestDetail->link_status = $payload['razorpay_invoice_status'];
            if($payload['razorpay_invoice_status']=='paid'){
              $paymentRequestDetail->booking_request_status = 'Payment Received';

            }
            $paymentRequestDetail->save();
            if($payload['razorpay_invoice_status']=='paid'){
                $bookingDetail = PropertyBooking::find(['id'=>$paymentRequestDetail->property_booking_id])->first();
                $paid_amount = $bookingDetail->paid_amount + $paymentRequestDetail->amount;
                $bookingDetail->paid_amount = $paid_amount;
                $totPaid = PropertyBookingPaymentRequest::where(['property_booking_id'=>$bookingDetail->id])->sum('amount');
                if($totPaid >=$bookingDetail->payable_amount){
                    $bookingDetail->booking_status = 'paid';
                    $bookingDetail->property_booking_status = 'Confirmed';
                }
                $bookingDetail->save();
            }
            return array('id'=>$paymentRequestDetail->id);
        }

        catch (\Exception $e) {

            dd($e->getMessage());

            Storage::disk('local')->put('razorpay_callback_error_'.time().'.txt', $e->getMessage());

            Log::error('Webhook signature verification failed', ['error' => $e->getMessage()]);

            return response()->json(['status' => 'failed', 'error' => 'Signature verification failed'], 400);

        }

    }

}