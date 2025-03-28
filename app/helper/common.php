<?php
use App\Models\TblLocation;
use App\Models\TblSitesetting;
use App\Models\TblGst;
use App\Models\RuPropertyAvailability;
use App\helper\MasterHelper;
use App\Models\RuPropertyPrice;
use App\Models\TblHome;
use App\Models\TblState;
use App\Http\Controllers\MinStayController;

function getLocationList(){
    return TblLocation::where('status', 1)->get()->toArray();
}

if (!function_exists('getLocationData')) {
    function getLocationData() {
        $location_data = TblLocation::where('tbl_location.status', 1)->whereNull('tbl_location.deleted_at')->get();
        return $location_data;
    }
}

if (!function_exists('getStatesWithLocations')) {
    function getStatesWithLocations()
    {
        $state_data = TblState::where('status', 1)
            ->with(['locations' => function ($query) {
                $query->where('status', 1)->whereNull('deleted_at'); 
            }])
            ->orderBy('name', 'asc')
            ->get();
        return $state_data->filter(function ($state) {
            return $state->locations->isNotEmpty(); 
        });
    }
}


function makeXmlRequest($xml){
    try{
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-type: application/x-www-form-urlencoded; charset=utf-8'
        ));
        curl_setopt($curl, CURLOPT_URL,env('RU_URL'));
        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
        if ($xml != ""){
            if(is_array($xml)){
                $xml = implode("&",$xml);
            }
            curl_setopt($curl, CURLOPT_POST,1);
            curl_setopt($curl, CURLOPT_POSTFIELDS,$xml);
        }
        $xml_result=curl_exec($curl);
        $result = simplexml_load_string($xml_result);
        curl_close($curl);
        $data = array('success'=>true, 'message'=>'Listed successfully', 'code'=>200, 'data'=>$result);
    }
    catch(Exception $e){
        $data = array('success'=>false, 'message'=>$e->getmessge(), 'code'=>500, 'data'=>null);
    }
    return $data;
}



function setting(){
    return TblSitesetting::first();
}


function getAppliedGst($price){
    return TblGst::where('slabs_start', '<=', $price)->where('slabs_upto', '>=', $price)->first();
}

function locations(){
     return DB::table('tbl_location')
                                        ->where('status', 1)
                                        ->whereNull('deleted_at')
                                        ->get();
}





function blockPropertyAvailabilityInRu($ru_id, $checkin_date, $checkout_date){
    $ck = date('Y-m-d', strtotime($checkout_date));
    if($checkout_date > $checkin_date){
        $checkoutDate = date('Y-m-d', strtotime($ck . '-1 day'));
    }
    else{
        $checkoutDate = $ck;
    }
    
    RuPropertyAvailability::where('ru_property_id', $ru_id)->whereBetween('availability_date', [$checkin_date, $checkoutDate])->update(['is_available'=>'no']);
}

function unBlockPropertyAvailabilityInRu($ru_id, $checkin_date, $checkout_date){
   
    $ck = date('Y-m-d', strtotime($checkout_date));
    $checkin_date = date('Y-m-d', strtotime($checkin_date));
    if($checkout_date > $checkin_date){
        $checkoutDate = date('Y-m-d', strtotime($ck . '-1 day'));
    }
    else{
        $checkoutDate = $ck;
    }
    RuPropertyAvailability::where('ru_property_id', $ru_id)->whereBetween('availability_date', [$checkin_date, $checkoutDate])->update(['is_available'=>'yes']);
    //   $xml = "<Push_PutAvbUnits_RQ>
    //             <Authentication>
    //                 <UserName>".config('ru.RU_USER_NAME')."</UserName>
    //                 <Password>".config('ru.RU_PASSWORD')."</Password>
    //             </Authentication>
    //             <MuCalendar PropertyID='".$ru_id."'>
    //                 <Date From='".date('Y-m-d', strtotime($checkin_date))."' To='".date('Y-m-d', strtotime($checkoutDate))."'>
    //                     <U>1</U>
    //                     <C>4</C>
    //                 </Date>
    //             </MuCalendar>
    //         </Push_PutAvbUnits_RQ>";
    // $xmlResponse = MasterHelper::makeXmlRequest($xml);
    // return $xmlResponse;
}


function propertyListByBasedLocation($ids, $guests){
    $query = TblHome::query();
    $query->whereIn('tbl_homes.id', $ids);
    $query->where('tbl_homes.status', 1);
    $query->where('tbl_homes.maximum_number_of_guests', '>=', $guests);
    $query->whereNull('tbl_homes.deleted_at');
    $query->leftJoin('tbl_location', 'tbl_homes.location_id', '=', 'tbl_location.id');
    $query->with(['price', 'homeImageVideo','locationData']);
    $query->whereHas('propertyAvailabilities', function ($query) {
        $query->where('availability_date', '>=', date('Y-m-01'));
        $query->where('is_available', '!=', 'no');
    });
    $properties = $query->get(['tbl_homes.*', 'tbl_location.id as location_id', 'tbl_location.location_name']);
    //$guestOptions  = guestOptions();
 
    $finalArray = array();
    foreach($properties as $key=>$property){
        $propertyCheckInDate = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'yes')->where('availability_date', '>=', date('Y-m-d'))->orderBy('availability_date', 'asc')->first();
        if(isset($propertyCheckInDate->availability_date)){
            $checkInDate =  $propertyCheckInDate->availability_date;
        }
        else{
            $checkInDate =  date('Y-m-d'); 
        }
       
        $checkOutDate = date('Y-m-d', strtotime($checkInDate. '+1 days'));
        $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($checkInDate)),date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))])->sum('price');
      
        if($price > 0){
            $property->price =  $price;
            $per_night_price = $price;
            if(setting()->website_markup){
                $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
            }
            $property->checkInDate =  $checkInDate;
            $property->checkOutDate =  $checkOutDate;
            $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
            $property->noOfNights =  (integer)$date_difference_count;  
            $property->per_room_price = $per_night_price/$property->no_of_bedrooms;
            $property->per_night_price =  $per_night_price;
            array_push($finalArray, $property);
        }
    }
    return $finalArray;
}


function propertyList($search_parameters){
    try {
        $no_of_guests = $search_parameters['guest_count'];
        $last_date =  date('Y-m-d', strtotime($search_parameters['departureDate']. '-1 days'));
        $checkin_date =  $search_parameters['arrivalDate'];
        if($last_date == $search_parameters['arrivalDate']){
            $date_difference_count = 1;
        }
        else{
            $date_difference_count = MasterHelper::getDateDifference($search_parameters['arrivalDate'], $search_parameters['departureDate']);
        }
        if($date_difference_count > 1){
            $date_difference_count = $date_difference_count;
        }
        else{
            $date_difference_count = 1;
        }
        $location_id = $search_parameters['location_id'];
        $query = TblHome::query();
        $query->when($location_id != '', function ($q) use ($location_id) {
            return $q->where('location_id', $location_id);
        });
        $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
            return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        });
        $list = $query->with(['additionalCharge', 'images'])->whereNotNull('ru_property_id')->get();
        
        $filtered_property_list = array();
        if(!empty($list)){
            foreach($list as $detail){
                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $search_parameters['departureDate'])->where('is_available', 'no')->count();
              
                $minStayController = new MinStayController();
                $minStay =  $minStayController->syncMinStay($checkin_date, $detail->id); 
                //$minStay =  1; 
                if($count ==0 ){
                    $price = 0;
                    $price = $initial_price = 0;

                    $price = RuPropertyPrice::where('ru_property_id', $detail->ru_property_id)->whereBetween('price_date', [$checkin_date, $last_date])->sum('price');
                    if($price >0  && (integer)$date_difference_count >= (integer)$minStay ){
                        $gst_amount = 0;
                        $gstPrecentage = 0;

                        $detail->price = $price;
                        $per_night_price = $price/$date_difference_count;
                        if(setting()->website_markup){
                        
                            $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
                        }
                        $detail->per_night_price = round($per_night_price);
                        $detail->per_room_price = $per_night_price/$detail->no_of_bedrooms;
                        $detail->initial_price = $price;
                        $detail->gst_amount = $gst_amount;
                        $detail->gst_percentage = $gstPrecentage;
                        $detail->noOfNights = $date_difference_count;
                     
                        $getAppliedGst  = getAppliedGst($price);
                        if($getAppliedGst){
                            $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                            $gst_amount = $precentageAmount;
                            $gstPrecentage = $getAppliedGst->gst_percentage;
                        }

                        $extra_no_of_guest = 0;
                        $extra_guest_charge = 0;

                       

                        if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){
                            if($detail->maximum_number_of_guests == $no_of_guests){
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                            }
                            else if($no_of_guests == $detail->maximum_number_of_guests){
                                $extra_no_of_guest = 1;
                            }
                            else{
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                            }
                            $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;
                            $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                            if($getAppliedGst){
                                $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;
                                $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                            }
                        }
                        $detail->extra_no_of_guest = $extra_no_of_guest;
                        $detail->final_extra_guest_charge = $extra_guest_charge;
                        array_push($filtered_property_list, $detail);
                    }
                }
            }
        }
     
        return $filtered_property_list;
    }
    catch (\Exception $e) {
        return $e->getMessage();
    }
}


function propertyListByState($search_parameters){
    try {
        $no_of_guests = $search_parameters['guest_count'];
        $last_date =  date('Y-m-d', strtotime($search_parameters['departureDate']. '-1 days'));
        $checkin_date =  $search_parameters['arrivalDate'];
        if($last_date == $search_parameters['arrivalDate']){
            $date_difference_count = 1;
        }
        else{
            $date_difference_count = MasterHelper::getDateDifference($search_parameters['arrivalDate'], $search_parameters['departureDate']);
        }
        if($date_difference_count > 1){
            $date_difference_count = $date_difference_count;
        }
        else{
            $date_difference_count = 1;
        }
        $location_id = $search_parameters['locations'];
        $query = TblHome::query();
        $query->when($location_id != '', function ($q) use ($location_id) {
            return $q->whereIn('location_id', $location_id);
        });
        $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
            return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        });
        $list = $query->with(['additionalCharge', 'images'])->whereNotNull('ru_property_id')->get();
        
        $filtered_property_list = array();
        if(!empty($list)){
            foreach($list as $detail){
                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $search_parameters['departureDate'])->where('is_available', 'no')->count();
              
                $minStayController = new MinStayController();
                $minStay =  $minStayController->syncMinStay($checkin_date, $detail->id); 
                //$minStay =  1; 
                if($count ==0 ){
                    $price = 0;
                    $price = $initial_price = 0;

                    $price = RuPropertyPrice::where('ru_property_id', $detail->ru_property_id)->whereBetween('price_date', [$checkin_date, $last_date])->sum('price');
                    if($price >0  && (integer)$date_difference_count >= (integer)$minStay ){
                        $gst_amount = 0;
                        $gstPrecentage = 0;

                        $detail->price = $price;
                        $per_night_price = $price/$date_difference_count;
                        if(setting()->website_markup){
                        
                            $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
                        }
                        $detail->per_night_price = round($per_night_price);
                        $detail->per_room_price = $per_night_price/$detail->no_of_bedrooms;
                        $detail->initial_price = $price;
                        $detail->gst_amount = $gst_amount;
                        $detail->gst_percentage = $gstPrecentage;
                        $detail->noOfNights = $date_difference_count;
                     
                        $getAppliedGst  = getAppliedGst($price);
                        if($getAppliedGst){
                            $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                            $gst_amount = $precentageAmount;
                            $gstPrecentage = $getAppliedGst->gst_percentage;
                        }

                        $extra_no_of_guest = 0;
                        $extra_guest_charge = 0;

                       

                        if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){
                            if($detail->maximum_number_of_guests == $no_of_guests){
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                            }
                            else if($no_of_guests == $detail->maximum_number_of_guests){
                                $extra_no_of_guest = 1;
                            }
                            else{
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                            }
                            $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;
                            $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                            if($getAppliedGst){
                                $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;
                                $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                            }
                        }
                        $detail->extra_no_of_guest = $extra_no_of_guest;
                        $detail->final_extra_guest_charge = $extra_guest_charge;
                        array_push($filtered_property_list, $detail);
                    }
                }
            }
        }
     
        return $filtered_property_list;
    }
    catch (\Exception $e) {
        return $e->getMessage();
    }
}

function getPriceAndAvalibility($ru_property_id){
    
    $property = TblHome::where('ru_property_id', $ru_property_id)->first('id');
    $checkInDate =  date('Y-m-d'); 
    $propertyCheckInDate = DB::table('ru_property_availabilities')
                    ->select('availability_date')
                    ->where('ru_property_id', $ru_property_id)
                    ->where('is_available', 'yes')
                    ->where('availability_date', '>=', $checkInDate)
                    ->orderBy('availability_date', 'asc')
                    ->first();
                    
    if(isset($propertyCheckInDate->availability_date)){
        $checkInDate =  $propertyCheckInDate->availability_date;
    }
    
    
    $minStayController = new MinStayController();
    $minStay = 1;
    $minStays = $minStayController->syncMinStay(date('Y-m-d', strtotime($checkInDate)), $property->id);
    
    if($minStays){
        $minStay = (integer)$minStays;
    }
    $checkOutDate = date('Y-m-d', strtotime($checkInDate . ' +' . $minStay . ' days'));
    $last_date =  date('Y-m-d', strtotime($checkOutDate. '-1 days'));
    if($last_date ==$checkInDate){
        $date_difference_count = 1;
    }
    else{
        $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
    }
    $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
    $price = RuPropertyPrice::where('ru_property_id', $ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($checkInDate)),date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))])->sum('price');
    if(setting()->website_markup){
        $price = $price +  ($price*setting()->website_markup)/100;
    }
    $price =$price/(int)$date_difference_count;
    return array('per_night_price'=>$price, 'next_available_date_from'=>$checkInDate, 'next_available_date_to'=>$checkOutDate);
}

function propertyListByLocation($ids, $guests){
    $query = TblHome::query();
    $query->whereIn('tbl_homes.id', $ids);
    $query->where('tbl_homes.status', 1);
    $query->where('tbl_homes.maximum_number_of_guests', '>=', $guests);
    $query->whereNull('tbl_homes.deleted_at');
    $query->leftJoin('tbl_location', 'tbl_homes.location_id', '=', 'tbl_location.id');
    $query->with(['price', 'images', 'amenities', 'tags', 'homeAmenities','locationData']);
    $properties = $query->get(['tbl_homes.*', 'tbl_location.id as location_id', 'tbl_location.location_name']);
    $finalArray = array();
    foreach($properties as $key=>$property){
        $propertyCheckInDate = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'yes')->where('availability_date', '>=', date('Y-m-d'))->orderBy('availability_date', 'asc')->first();
        if(isset($propertyCheckInDate->availability_date)){
            $checkInDate =  $propertyCheckInDate->availability_date;
        }
        else{
            $checkInDate =  date('Y-m-d'); 
        }
        $minStayController = new MinStayController();
        $minStay = 1;
        $minStays = $minStayController->syncMinStay(date('Y-m-d', strtotime($checkInDate)), $property->id);
        
        if($minStays){
            $minStay = (integer)$minStays;
        }
        $checkOutDate = date('Y-m-d', strtotime($checkInDate . ' +' . $minStay . ' days'));
        $last_date =  date('Y-m-d', strtotime($checkOutDate. '-1 days'));
        if($last_date ==$checkInDate){
            $date_difference_count = 1;
        }
        else{
            $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
        }
        $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($checkInDate)),date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))])->sum('price');
        $per_night_price = $price;
        if($price > 0){
            $property->price =  $price/(int)$date_difference_count;
            $per_night_price = $price/(int)$date_difference_count;
            if(setting()->website_markup){
                $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
            }
            $property->checkInDate =  $checkInDate;
            $property->checkOutDate =  $checkOutDate;
            $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
            $property->noOfNights =  (integer)$date_difference_count;  
            $property->per_room_price = $per_night_price/$property->no_of_bedrooms;
            $property->per_night_price =  round($per_night_price);
            array_push($finalArray, $property);
        }
    }
    return $finalArray;
}

function propertyListByLocationFilter($ids, $guests,$min_price = null, $max_price = null){
    $query = TblHome::query();
    $query->whereIn('tbl_homes.id', $ids);
    $query->where('tbl_homes.status', 1);
    $query->where('tbl_homes.maximum_number_of_guests', '>=', $guests);
    $query->whereNull('tbl_homes.deleted_at');
    $query->leftJoin('tbl_location', 'tbl_homes.location_id', '=', 'tbl_location.id');
    $query->with(['price', 'images', 'amenities', 'tags', 'homeAmenities','locationData']);
    $properties = $query->get(['tbl_homes.*', 'tbl_location.id as location_id', 'tbl_location.location_name']);
    $finalArray = array();
    foreach($properties as $key=>$property){
        $propertyCheckInDate = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'yes')->where('availability_date', '>=', date('Y-m-d'))->orderBy('availability_date', 'asc')->first();
        if(isset($propertyCheckInDate->availability_date)){
            $checkInDate =  $propertyCheckInDate->availability_date;
        }
        else{
            $checkInDate =  date('Y-m-d'); 
        }
        $minStayController = new MinStayController();
        $minStay = 1;
        $minStays = $minStayController->syncMinStay(date('Y-m-d', strtotime($checkInDate)), $property->id);
        
        if($minStays){
            $minStay = (integer)$minStays;
        }
        $checkOutDate = date('Y-m-d', strtotime($checkInDate . ' +' . $minStay . ' days'));
        $last_date =  date('Y-m-d', strtotime($checkOutDate. '-1 days'));
        if($last_date ==$checkInDate){
            $date_difference_count = 1;
        }
        else{
            $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
        }
        $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($checkInDate)),date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))])->sum('price');
        $per_night_price = $price;
        if($price > 0){
            $property->price =  $price/(int)$date_difference_count;
            $per_night_price = $price/(int)$date_difference_count;
            if(setting()->website_markup){
                $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
            }
            $property->checkInDate =  $checkInDate;
            $property->checkOutDate =  $checkOutDate;
            $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
            $property->noOfNights =  (integer)$date_difference_count;  
            $property->per_room_price = $per_night_price/$property->no_of_bedrooms;
            $property->per_night_price =  round($per_night_price);
            //array_push($finalArray, $property);

            // Apply price filter if the prices are provided
            if (($min_price !== null && $per_night_price >= $min_price) && ($max_price !== null && $per_night_price <= $max_price)) {
                array_push($finalArray, $property);
            } elseif ($min_price === null && $max_price === null) {
                // If no price filter is applied, add all properties
                array_push($finalArray, $property);
            }

        }
    }
    return $finalArray;
}

function reservationXmlRequest($booking){
    
   
}
