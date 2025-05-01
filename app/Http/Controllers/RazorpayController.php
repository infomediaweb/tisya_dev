<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Services\RazorpayService;
use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use App\Models\TblHomeImageVideo;
use Mail;
use DB;


class RazorpayController extends Controller{
    
    public function handleWebhookCallBack(Request $request) {
        try {
            if(!$request->all()){
                return response()->json([
                    'status' => true,
                    'message' => "Invalid Request",
                ], 200);
            }
            
            if($request->missing('razorpay_invoice_id')){
                return response()->json([
                    'status' => true,
                    'message' => "Invalid Request",
                ], 200);
            }
            
            $payload = array(
                'razorpay_invoice_id'=>$request->query('razorpay_invoice_id'),
                'razorpay_invoice_receipt'=>$request->query('razorpay_invoice_receipt'),
                'razorpay_invoice_status'=>$request->query('razorpay_invoice_status'),
                'razorpay_payment_id'=>$request->query('razorpay_payment_id'),
                'razorpay_signature'=>$request->query('razorpay_signature')
            );
            $handleCallBackRes = RazorpayService::handleWebhookCallBack($payload);
            
            return redirect()->route('payment.received.thankyou', ['id' => $handleCallBackRes['id']]);
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
    
    
    public function paymentThankyou($id) {
        
        $paymentRequestInfo = PropertyBookingPaymentRequest::find(['id'=>$id])->first();
        
        $data = PropertyBooking::withTrashed()->with('home')->where('id', $paymentRequestInfo->property_booking_id)->first();
        $prorpertyAssetsDetail = TblHomeImageVideo::where('home_id', $data->home->id)->where('type', 'image')->first();
        return view('emails.thankyou', compact('paymentRequestInfo', 'data', 'prorpertyAssetsDetail'));
    }
}
