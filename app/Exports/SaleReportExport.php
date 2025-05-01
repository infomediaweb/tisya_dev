<?php

namespace App\Exports;

use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\helper\MasterHelper;

class SaleReportExport implements FromCollection, WithHeadings, WithStyles, WithColumnFormatting{
   // use Exportable;

    protected $request;
    public $data;

    public function __construct($request){
        $this->request = $request;
    }

    public function collection(){
        $reqParameter = (object) $this->request;
        
       
        $list = PropertyBooking::query()
            ->when(isset($reqParameter->searchChannel) && $reqParameter->searchChannel != '', function ($query) use ($reqParameter) {
                return $query->where('channel', $reqParameter->searchChannel);
            })
            ->when(isset($reqParameter->searchPropertyId) && $reqParameter->searchPropertyId != '', function ($query) use ($reqParameter) {
                return $query->where('property_id', $reqParameter->searchPropertyId);
            })
            ->when(isset($reqParameter->searchPaymentStatus) && $reqParameter->searchPaymentStatus != '', function ($query) use ($reqParameter) {
                return $query->where('property_booking_status', $reqParameter->searchPaymentStatus);
            })
            // ->when(isset($reqParameter->checkin_date) && isset($reqParameter->checkout_date) && $reqParameter->checkin_date != '' && $reqParameter->checkout_date != '', function ($query) use ($reqParameter) {
            //     return $query
            //         ->whereDate('property_bookings.created_at', '>=', $reqParameter->checkin_date)
            //         ->whereDate('property_bookings.created_at', '<=', $reqParameter->checkout_date);
            // })
            ->when(isset($reqParameter->checkin_date) && isset($reqParameter->checkout_date) && $reqParameter->checkin_date != '' && $reqParameter->checkout_date != '', function ($query) use ($reqParameter) {
                
                
                if ($reqParameter->searchtype == 'checkin') {
                    return $query->whereBetween('property_bookings.checkin_date', [$reqParameter->checkin_date, $reqParameter->checkout_date]);
                }
                if ($reqParameter->searchtype == 'BookingDate') {
                    return $query->whereBetween('property_bookings.created_at' , [$reqParameter->checkin_date, $reqParameter->checkout_date]);
                }

                
                // return $query
                //     ->whereDate('property_bookings.created_at', '>=', $reqParameter->checkin_date)
                //     ->whereDate('property_bookings.created_at', '<=', $reqParameter->checkout_date);


            })
            
            
            ->where('property_bookings.payable_amount', '>', 0)
            //->where('property_bookings.booking_status', 'paid')
            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
            ->with('paymentRequests')
            ->orderBy('property_bookings.created_at', 'desc')
            ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);

        $finalData = [];
        foreach ($list as $value) {
            $paymentModes = $value->paymentRequests->pluck('payment_mode')->unique()->implode(', ');
            $guest_detail = $value->customer_detail;
            
            $lastDateOfMonth = date("Y-m-t", strtotime(date('Y-m-d')));
            
            
            
            
            $finalData[] = [
                    'booking_id' => $value->booking_id,
                    'home_name' => $value->home_name,
                    'location' => $value->location,
                    'booking_date' => $value->created_at,
                    'checkin' => $value->checkin_date,
                    'checkout' => $value->checkout_date,
                    'guest_email_id' => $guest_detail['email'],
                    'guest_name' => $guest_detail['first_name'] . ' ' . $guest_detail['last_name'],
                    'guest_mobile_no' => (string) $guest_detail['mobile_number'].' ',
                    'channel' => $value->channel,
                    'base_price' => $value->total_amount,
                    'tax' => $value->tax ? $value->tax . "%" : '',
                    'taxable_amount' => $value->taxable_amount,
                    'payable_amount' => $value->payable_amount,
                    'payment_received' => $value->channel !== 'PMS' ? $value->payable_amount : $value->paid_amount,
                    'payment_mode' => $paymentModes,
                    'invoice' => "",
                ]; 
            
           
            
            
        }

        return collect($finalData);
    }

    public function headings(): array
    {
        return ["Booking ID", "Property Name", "Location", "Booking Date", "Checkin Date","Checkout Date", "Guest Email ID", "Guest Name", "Guest Mobile No.", "Channel", "Base Price", "Tax", "Tax Amount", "Payable", "Paid", "Payment Mode", "Invoice No."];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_TEXT, // Ensure Guest Mobile No is stored as text
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Apply bold styling to the first row (headers)
        $sheet->getStyle('A1:P1')->getFont()->setBold(true);
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(20);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        return [];
    }
}
