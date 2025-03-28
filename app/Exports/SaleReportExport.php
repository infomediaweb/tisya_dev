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

class SaleReportExport implements FromCollection,  WithHeadings{

    protected $request;
    public $data;
    public function __construct($request){
        $this->request = $request;

    }
    /**
    * @return \Illuminate\Support\Collection
    */
    // public function collection(){
    //     $reqParameter = (object)$this->request;

    //     $list = PropertyBooking::query()

    //         ->when(isset($reqParameter->searchChannel) && $reqParameter->searchChannel !='', function ($query) use ($reqParameter) {

    //             return $query->where('channel', $reqParameter->searchChannel);
    //         })
            
    //         ->when(isset($reqParameter->searchPaymentStatus) && $reqParameter->searchPaymentStatus != '', function ($query) use ($reqParameter) {
    //             return $query->where('property_booking_status', $reqParameter->searchPaymentStatus);
    //         })
            
    //         ->when(isset($reqParameter->searchPropertyId) && $reqParameter->searchPropertyId !='', function ($query) use ($reqParameter) {
    //             return $query->where('property_id', $reqParameter->searchPropertyId);
    //         })

    //         ->when(isset($reqParameter->checkin_date) && isset($reqParameter->checkout_date) && $reqParameter->checkin_date !='' && $reqParameter->checkout_date !='', function ($query) use ($reqParameter) {
               
    //           if ($reqParameter->searchtype == 'checkin') {
    //                 return $query->whereDate('property_bookings.checkin_date', '>=', $reqParameter->checkin_date);
    //             }
    //             if ($reqParameter->searchtype == 'BookingDate') {
    //                 return $query->whereDate('property_bookings.created_at', '>=', $reqParameter->checkin_date);
    //             }
               
    //         })

    //         ->where('property_bookings.payable_amount', '>', 0)
    //         ->where('property_bookings.booking_status', 'paid')
    //         ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('paymentRequests')
    //         ->orderBy('property_bookings.created_at', 'desc')
    //         ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
    //     $finalData = array();
    //     $totalPayableAmount = 0;
    //     $totalPendingAmount = 0;
    //     $totalPaidAmount = 0;
    //     $totalTaxAmount =0;
    //     foreach($list as $key=>$value){
    //         $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $value->id)->where('booking_request_status', 'Payment Received')->sum('amount');
    //         $guest_detail = $value->customer_detail;
    //         $detail = array();
    //         $detail['booking_id'] = $value->booking_id;
    //         $detail['home_name'] = $value->home_name;
    //         $detail['location'] = $value->location;
    //         $detail['guest_email_id'] = $guest_detail['email'];
    //         $detail['guest_name'] = $guest_detail['first_name'].' '.$guest_detail['last_name'];
    //         $detail['guest_mobile_no'] = $guest_detail['mobile_number'];
    //         $detail['channel'] = $value->channel;
    //         $detail['payable_amount'] = 'INR '.$value->payable_amount;
    //         if($value->channel !='PMS'){
    //             $detail['payment_received'] = 'INR '.$value->payable_amount;
    //         }
    //         else{
    //             $detail['payment_received'] = ($paidAmount != 0)?'INR '.$paidAmount:"";
    //         }
    //         $detail['tax'] = $value->tax?$value->tax."%":'';
    //         $detail['taxable_amount'] = 'INR '.$value->taxable_amount;
    //         array_push($finalData, $detail);
    //         $totalPayableAmount += $value->payable_amount;
    //         $totalPendingAmount += ($value->payable_amount - $paidAmount);
    //         $totalPaidAmount += $paidAmount;
    //         $totalTaxAmount +=$value->taxable_amount;
    //     }

    //     $totals = array(
    //         'booking_id' => '',
    //         'home_name' => '',
    //         'location' => '',
    //         'guest_email_id' => '',
    //         'guest_name' => '',
    //         'guest_mobile_no' => '',
    //         'channel' => 'Total',
    //         'payable_amount' => 'INR '.$totalPayableAmount,
           
    //         'payment_received' => 'INR '.$totalPaidAmount,
    //         'tax' => '',
    //         'taxable_amount' => 'INR '.$totalTaxAmount
    //     );
    //     array_push($finalData, $totals);
    //     $this->data = collect($finalData);
    //     return collect($finalData);

    // }


public function collection()
    {
        $reqParameter = (object) $this->request;

        $list = PropertyBooking::query()
            ->when(isset($reqParameter->searchChannel) && $reqParameter->searchChannel != '', function ($query) use ($reqParameter) {
                return $query->where('channel', $reqParameter->searchChannel);
            })
            ->when(isset($reqParameter->searchPaymentStatus) && $reqParameter->searchPaymentStatus != '', function ($query) use ($reqParameter) {
                return $query->where('property_booking_status', $reqParameter->searchPaymentStatus);
            })
            ->when(isset($reqParameter->searchPropertyId) && $reqParameter->searchPropertyId != '', function ($query) use ($reqParameter) {
                return $query->where('property_id', $reqParameter->searchPropertyId);
            })
            ->when(isset($reqParameter->checkin_date) && isset($reqParameter->checkout_date) && $reqParameter->checkin_date != '' && $reqParameter->checkout_date != '', function ($query) use ($reqParameter) {
                
                
                if ($reqParameter->searchtype == 'checkin') {
                    return $query->whereDate('property_bookings.checkin_date', '>=', $reqParameter->checkin_date);
                }
                if ($reqParameter->searchtype == 'BookingDate') {
                    return $query->whereDate('property_bookings.created_at', '>=', $reqParameter->checkin_date);
                }

                
                // return $query
                //     ->whereDate('property_bookings.created_at', '>=', $reqParameter->checkin_date)
                //     ->whereDate('property_bookings.created_at', '<=', $reqParameter->checkout_date);


            })
            ->where('property_bookings.payable_amount', '>', 0)
          //  ->where('property_bookings.booking_status', 'paid')
            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
            ->with('paymentRequests')
            ->orderBy('property_bookings.created_at', 'desc')
            ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);

        $finalData = [];
        foreach ($list as $value) {
            $guest_detail = $value->customer_detail;
            $paymentModes = $value->paymentRequests->pluck('payment_mode')->implode(', ');
            $finalData[] = [
                'booking_id' => $value->booking_id,
                'home_name' => $value->home_name,
                'location' => $value->location,
                'guest_email_id' => $guest_detail['email'],
                'guest_name' => $guest_detail['first_name'] . ' ' . $guest_detail['last_name'],
                'guest_mobile_no' => (string) $guest_detail['mobile_number'].' ',
                'channel' => $value->channel,
                'base_price' => $value->total_amount,
                'tax' => $value->tax ? $value->tax . "%" : '',
                'taxable_amount' => $value->taxable_amount,
                'payable_amount' => $value->payable_amount,
                'payment_received' => $value->channel !== 'PMS' ? $value->payable_amount : $value->paid_amount,
                'invoice_number' => "",
                'payment_mode' => $paymentModes,
                'checkin_date' => $value->checkin_date,
                'checkout_date' => $value->checkout_date,
            ];
        }

        return collect($finalData);
    }

    public function styles(Worksheet $sheet){
        $totalRowIndex = end($this->data);
        return [
            $totalRowIndex => ['font' => ['bold' => true]],
        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
     
      public function headings(): array
    {
        return ["Booking ID", "Property Name", "Location", "Guest Email ID", "Guest Name", "Guest Mobile No.", "Channel", "Base Price", "Tax", "Tax Amount", "Payable", "Paid","Invoice number","Mode of payment","Checkin Date","Checkout Date"];
    }

     
    // public function headings(): array{
    //     return ["Booking ID", "Property Name", "Location" , "Guest Email ID", "Guest Name", "Guest Mobile No.", "Channel", "Total Amount", "Paid Amount", "Tax", "Tax Amount"];
    // }
}
