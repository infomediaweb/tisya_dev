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
use App\Models\DiscountCouponCodeMapping;

class CouponExport implements FromCollection,  WithHeadings{

    protected $request;
    public function __construct($request){
        $this->request = $request;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection(){
        $reqParameter = $this->request;

        $list = DiscountCouponCodeMapping::query()
            ->when(isset($reqParameter['id']) && $reqParameter['id'], function ($query) use ($reqParameter) {
                return $query->where('discount_coupon_id', $reqParameter['id']);
            })->get();

        $finalData = array();
        foreach($list as $key=>$value){
            $detail['coupon_code'] = $value->code;
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
        return ["Coupon Code"];
    }
}
