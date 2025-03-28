<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyPrice;
use Maatwebsite\Excel\Facades\Excel;
use App\helper\MasterHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PropertyBooking;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyBookingPaymentRequest;
use Illuminate\Support\Collection;
use App\Models\BookingGuestId;
use App\Models\BookingEnquiry;
use App\Exports\BookingExport;
use App\Models\TblState;
use Carbon\Carbon;

class RuLiveNotificationWebhookController extends Controller{

    //---------------- This method use for registring the webhook url------//
    //---- URL for webhook registration -> https://pmsdemo.tempsite.in/ru/set/booking/webhook  ----//
    public function setLiveNotificationWebhook(){


        
    }

    
    
    public function getLiveNotificationWebhook(Request $request){
        Storage::disk('local')->put('ru_live_notification_availability'.$request->query('Type').'_'.time().'.txt', $request);
        
        if($request->query('Type')=='PropertyAvailability'){
            $dateFromStringWithoutZ = rtrim($request->query('DateFrom'), 'Z');
            $dateFromC = Carbon::parse($dateFromStringWithoutZ);

            $dateToStringWithoutZ = rtrim($request->query('DateTo'), 'Z');
            $dateToC = Carbon::parse($dateToStringWithoutZ);

            $dateFrom =  date('Y-m-d', strtotime($dateFromC->toDateTimeString()));
            $dateTo =  date('Y-m-d', strtotime($dateToC->toDateTimeString()));
            $ruId =  $request->query('PropertyId');
            
            $startDate = Carbon::create($dateFrom); // YYYY, MM, DD
            $endDate = Carbon::create($dateTo); // YYYY, MM, DD
            
            
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                $cDate = $date->toDateString();
                $xmlReqForPropertyPrice = "<Pull_ListPropertyAvailabilityCalendar_RQ>
                    <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <PropertyID>".$ruId."</PropertyID>
                    <DateFrom>".$cDate."</DateFrom>
                    <DateTo>".$cDate."</DateTo>
                </Pull_ListPropertyAvailabilityCalendar_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);

                if($ruPropertyPriceResponse){
                    if(isset($ruPropertyPriceResponse['data']['PropertyCalendar']['CalDay']['IsBlocked'])){
                        $isAvailable =  'yes';
                        if($ruPropertyPriceResponse['data']['PropertyCalendar']['CalDay']['IsBlocked']=='true'){
                            $isAvailable = 'no';
                        }
                        $detail = array();
                        $detail['is_available'] = $isAvailable;
                        $detail['availability_date'] = $cDate;
                        $detail['ru_property_id'] = $ruId;
                        $count = RuPropertyAvailability::where(['ru_property_id'=>$ruId, 'availability_date'=>$cDate])->count();
                        if($count > 0){
                            RuPropertyAvailability::where(['ru_property_id'=>$ruId, 'availability_date'=>$cDate])->update($detail);
                            
                        }
                        else{
                            RuPropertyAvailability::create($detail);
                        }
                    }
                }
            }
        }
    }
}
