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

class PoliceVerificationReport implements FromCollection,  WithHeadings{

    protected $request;
    public $data;
    public function __construct($request){
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        $reqParameter = (object)$this->request;
        $list = PropertyBooking::query()

            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('paymentRequests')
            ->orderBy('property_bookings.created_at', 'desc')
            ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
        $finalData = array();
        $totalPayableAmount = 0;
        $totalPendingAmount = 0;
        $totalPaidAmount = 0;


        foreach($list as $key=>$value){
            $guest_detail = $value->customer_detail;
            $name = $guest_detail['first_name'].' '.$guest_detail['last_name'];
            $email = $guest_detail['email'];
            $mobile_no = $guest_detail['mobile_number'];

            $detail = array();
            $detail['guest_name'] = $guest_detail['first_name'].' '.$guest_detail['last_name'];
            $detail['guest_email_id'] = $guest_detail['email'];
            $detail['guest_mobile_no'] = $guest_detail['mobile_number'];
            $detail['checkin_date'] = $value->checkin_date;
            $detail['checkout_date'] = $value->checkout_date;

            if(isset($reqParameter->name) || isset($reqParameter->email) || isset($reqParameter->mobile_no)){
                if(isset($reqParameter->name)  && !isset($reqParameter->email) && !isset($reqParameter->email)){
                    if(str_contains(strtolower($name), strtolower($reqParameter->name))){
                        array_push($finalData, $detail);
                    }
                }
                if(isset($reqParameter->email) && !isset($reqParameter->name) && !isset($reqParameter->mobile_no)){
                    if($email !=[]){
                        if(str_contains($email, $reqParameter->email)){
                            array_push($finalData, $detail);
                        }
                    }
                }
                if(isset($reqParameter->mobile_no) && !isset($reqParameter->name) && !isset($reqParameter->email)){
                    if(str_contains($mobile_no, $reqParameter->mobile_no)){
                        array_push($finalData, $detail);
                    }
                }

                if(isset($reqParameter->name) && isset($reqParameter->email) && !isset($reqParameter->mobile_no)){
                    if($email !=[]){
                        if(str_contains(strtolower($name), strtolower($reqParameter->name)) && str_contains($email, $reqParameter->email)){
                            array_push($finalData, $detail);
                        }
                    }
                }
                if(!isset($reqParameter->name) && isset($reqParameter->email) && isset($reqParameter->mobile_no)){
                    if($email !=[]){
                        if(str_contains(strtolower($email), strtolower($reqParameter->email)) && str_contains(strtolower($mobile_no), strtolower($reqParameter->mobile_no))){
                            array_push($finalData, $detail);
                        }
                    }
                }

                if(isset($reqParameter->name) && !isset($reqParameter->email) && isset($reqParameter->mobile_no)){
                    if(str_contains(strtolower($name), strtolower($reqParameter->name)) && str_contains(strtolower($mobile_no), strtolower($reqParameter->mobile_no))){
                        array_push($finalData, $detail);
                    }

                }

                if(isset($reqParameter->name) && isset($reqParameter->email) && isset($reqParameter->mobile_no)){
                    if($email !=[]){
                        if(str_contains(strtolower($name), strtolower($request->name)) && str_contains(strtolower($email), strtolower($request->email)) && str_contains(strtolower($mobile_no), strtolower($request->mobile_no))){
                            array_push($finalData, $detail);
                        }
                    }
                }
            }
            else{
                array_push($finalData, $detail);
            }
        }
        return collect($finalData);
    }




    /**
     * Write code on Method
     *
     * @return response()
     */
    public function headings(): array{
        return ["Customer Name", "Email Id", "Mobile No." , "Checkin Date", "Checkout Date"];
    }
}
