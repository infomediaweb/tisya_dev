<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\RuPropertyMinstay;
use App\Models\TblHome;
use App\Models\RuMinStay;
use DB;


class MinStayController extends Controller{
   
    public function syncMinStay($date, $id){
        set_time_limit(0);
        $property = TblHome::where('id', $id)->whereNotNull('ru_property_id')->first();
       
        $date = date('Y-m-d', strtotime($date));
        $minStayNo = 1;
        
        $minStayDetail = RuMinStay::where(['ru_property_id'=>$property->ru_property_id, 'minstay_date'=>$date])->first();
        if($minStayDetail){
            $minStayNo = $minStayDetail->minstay;
        }
        return $minStayNo;
    }


    public function syncMinStayFromTo($date, $id){
        set_time_limit(0);
        $property = TblHome::where('id', $id)->whereNotNull('ru_property_id')->first();
        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];

        $from = $date;
        $to = date('Y-m-d', strtotime($date . ' +180 day'));
        $minStay = 1;

        $minStayDetail = RuMinStay::where(['ru_property_id'=>$property->ru_property_id])->whereBetween('minstay_date', [$from, $to])->get(['minstay_date', 'minstay'])->toArray();

        $datesWithMinStay = array();
        if($minStayDetail){
            $datesWithMinStay = array_column($minStayDetail, 'minstay', 'minstay_date');
        }
        return $datesWithMinStay;
    }
}
