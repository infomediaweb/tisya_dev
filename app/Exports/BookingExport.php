<?php



namespace App\Exports;



use App\Models\User;

use App\Models\PropertyBooking;

use App\Models\PropertyBookingPaymentRequest;

use Illuminate\Contracts\Support\Responsable;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\FromQuery;

use Maatwebsite\Excel\Concerns\Exportable;

use Maatwebsite\Excel\Concerns\withMapping;

use Illuminate\Support\Collection;



class BookingExport implements FromCollection,  WithHeadings{



    protected $request;

    public function __construct($request){

        $this->request = $request;

    }

    /**

    * @return \Illuminate\Support\Collection

    */

    public function collection(){
            $reqParameter = $this->request;
            
            $list = PropertyBooking::query()
                ->when(!empty($reqParameter['property_id']), function ($query) use ($reqParameter) {
                    return $query->where('property_id', $reqParameter['property_id']);
                })
                ->when(!empty($reqParameter['booking_id']), function ($query) use ($reqParameter) {
                    return $query->where('booking_id', $reqParameter['booking_id']);
                })
                ->when(!empty($reqParameter['channel']), function ($query) use ($reqParameter) {
                    return $query->where('channel', $reqParameter['channel']);
                })
                ->when(!empty($reqParameter['payment_status']), function ($query) use ($reqParameter) {
                    return $query->where('booking_status', $reqParameter['payment_status']);
                })
                ->when(!empty($reqParameter['booking_status']), function ($query) use ($reqParameter) {
                    return $query->where('property_booking_status', $reqParameter['booking_status']);
                })
                ->when(!empty($reqParameter['created_by']), function ($query) use ($reqParameter) {
                    return $query->where('created_by', $reqParameter['created_by']);
                })
                // Type-based check-in/out filtering
                ->when(isset($reqParameter['type']) && !empty($reqParameter['checkin_date']) && !empty($reqParameter['checkout_date']), function ($query) use ($reqParameter) {
                    return $reqParameter['type'] === 'checkin'
                        ? $query->whereBetween('checkin_date', [$reqParameter['checkin_date'], $reqParameter['checkout_date']])
                        : $query->whereBetween('checkout_date', [$reqParameter['checkin_date'], $reqParameter['checkout_date']]);
                })
                // Default date filter if `type` is not provided
                ->when(empty($reqParameter['type']) && !empty($reqParameter['checkin_date']) && !empty($reqParameter['checkout_date']), function ($query) use ($reqParameter) {
                    return $query->whereBetween('checkin_date', [$reqParameter['checkin_date'], $reqParameter['checkout_date']]);
                })
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                ->with('paymentRequests')
                ->orderBy(isset($reqParameter['type']) && $reqParameter['type'] === 'checkin' ? 'checkin_date' : 'checkout_date', 'desc')
                ->get([
                    'tbl_homes.home_name', 
                    'tbl_homes.home_type', 
                    'tbl_homes.state', 
                    'tbl_homes.location', 
                    'property_bookings.*'
                ]);


        $finalData = array();
        foreach($list as $key=>$value){
            $guest_detail = $value->customer_detail;
            $detail = array();
            $detail['booking_id'] = $value->booking_id;
            $detail['home_name'] = $value->home_name;
            $detail['guest_name'] = $guest_detail['first_name'].' '.$guest_detail['last_name'];
            $detail['guest_mobile_no'] = $guest_detail['mobile_number'];
            $detail['guest_email_id'] = $guest_detail['email'];
            $detail['channel'] = $value->channel;
            $detail['Price'] = 'INR '.$value->payable_amount;
            $detail['payment_received'] = ($value->paid_amount != 0)?'INR '.$value->paid_amount:'-';
            $detail['payment_status'] = ucfirst($value->booking_status);
            $detail['booking_status'] = $value->property_booking_status;
            $detail['checkin_date'] = $value->checkin_date;
            $detail['checkout_date'] = $value->checkout_date;
            array_push($finalData, $detail);
        }
        return collect($finalData);
    }



    /**

     * Write code on Method

     *

     * @return response()

     */

    public function headings(): array{
        return ["Booking ID", "Property Name", "Guest Name" , "Guest Mobile No", "Guest Email ID", "Channel", "Price", "Payment Received", "Payment Status", "Booking Status", "Checkin Date", "Checkout Date"];

    }

}

