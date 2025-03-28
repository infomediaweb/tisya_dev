<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblTestimonial;
use Livewire\Component;
use App\Models\TblLocation;
use App\Models\TblOurDifference;
use App\Models\TblSpecialInvitation;
use App\Models\TblHomeBanner;
use App\Models\TblSecondHome;
use App\Models\HomeFooterBanner;
use App\Models\TblBlog;
use App\Models\TblJoinOurNetworkIntro;
use App\Models\TblJoinOurNetworkFaqs;
use App\Models\TblAboutUs;
use App\Models\TblTeam;
use App\Models\TblFaq;
use App\Models\TblCollection;
use App\Models\TblHome;
use App\Models\TblHomeImageVideo;
use App\Models\HomeAddventure;
use App\Models\DiscountCouponCodeMapping;
use App\helper\MasterHelper;
use App\Models\RuPropertyPrice;
use App\Models\TblTag;
use App\Models\TblState;
use App\Models\TblHomeCollection;
use App\Models\TblHomeReview;
use App\Models\TblHomeTags;
use App\Models\TblHomeType;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\MinStayController;
use App\Models\PropertyBooking;
use DB;
use DateTime;


class IndexController extends Controller{



 public function index(){
        session()->forget([
            'location_name',
            'checkin_date',
            'checkout_date',
            'city_id',
            'total_guests',
            'adultsCount',
            'childrenCount',
        ]);
       
//         $properties = TblHome::with([
//     'images' => function($subquery) {
//         $subquery->take(6);
//     },
//     'locationData'
// ])
// ->where('show_on_home', 1)
// ->where('status', 1)
// ->whereNull('deleted_at')
// ->get();

$properties = TblHome::with(['images','locationData','tags'])
        ->where('show_on_home', 1)
        ->where('status', 1)
        ->whereNull('deleted_at')
        ->whereHas('propertyAvailabilities', function ($query) {
              $query->where('is_available', '!=', 'no')->where('availability_date', '>', date('Y-m-d') );
          })
        ->get();

        
        $propertiesApartment = TblHome::with(['images','locationData','tags'])
        ->where('show_on_apartment', 1)
        ->where('status', 1)
        ->whereNull('deleted_at')
        ->whereHas('propertyAvailabilities', function ($query) {
              $query->where('is_available', '!=', 'no')->where('availability_date', '>', date('Y-m-d') );
          })
        ->get();
        
        $locations = TblLocation::with('state') 
         ->where('status', 1) 
          ->where('show_on_location_page', 1) 
          ->get();
          $blogs = TblBlog::where('status', 1)->where('show_on_blog_page',1)->get();
         $collections = TblCollection::query()
         ->where('status', 1)
          ->where('show_on_collection_page', 1)
           ->get();

         $specialoffers = TblSpecialInvitation::where('status', 1)
    ->with('discountCoupon')
    ->orderBy('position', 'asc') 
    ->take(3) // Limit the results
    ->get();

         
       $addventures = HomeAddventure::all();

        $footerHomeData = HomeFooterBanner::first();
        $footerHomeData->list_content = json_decode($footerHomeData->list_content, true);
        $home_banner = TblHomeBanner::with('homeBannerImage','homeBannerVideo')->where('status', 1)->whereNull('deleted_at')->first();
        
        return view('frontend.index', compact('properties','home_banner', 'propertiesApartment', 'footerHomeData','locations','addventures','blogs','specialoffers','collections'));
    }
    
  
       
   

// public function properties(Request $request){

//     $query = TblHome::with(['images', 'amenities'])
//         ->where('status', 1)
//         ->whereNull('deleted_at');
  
//     $properties = $query->get();
//     $totalStays = TblHome::where('status', 1)
//         ->whereNull('deleted_at')
//         ->count();
//     return view('frontend.properties.properties', [
//         'properties' => $properties, 
//         'totalStays' => $totalStays, 
                 
//     ]);
       
//     }

    public function properties(Request $request){
        $guestCount = 1;
        $onTopPropertyIdArray = TblHome::where('status', 1)->get()->pluck('id')->toArray();
        $AllPropertyArray = propertyListByLocation($onTopPropertyIdArray, $guestCount);
        
        $sortOrder = $request->get('sort', 'low_to_high');
        usort($AllPropertyArray, function ($a, $b) use ($sortOrder) {
            $priceA = $a->per_night_price * $a->noOfNights;
            $priceB = $b->per_night_price * $b->noOfNights;
    
            if ($sortOrder === 'high_to_low') {
                return $priceB <=> $priceA;
            }
            return $priceA <=> $priceB;
        });
        
        $defaultPerPage = 12;
        $currentPage = $request->get('page', 1);
        $perPage = $defaultPerPage * $currentPage;
        $currentPageItems = array_slice($AllPropertyArray, 0, $perPage);
        
        $properties = new LengthAwarePaginator(
            $currentPageItems,
            count($AllPropertyArray),
            $defaultPerPage, 
            $currentPage,
            ['path' => url()->current()] 
        );
        $totalStays = count($currentPageItems);
      //  dd($properties);
            return view('frontend.properties.properties', [
                'properties' => $properties, 
                'totalStays' => $totalStays, 
                'sortOrder' => $sortOrder, 
            ]);
    }


    public function collectionsPropertyList(Request $request){
        $guestCount = 1;
        
        
       $collections = TblCollection::get()->pluck('id');
       $Home_id = TblHomeCollection::whereIn('collection_id', $collections)->get()->pluck('home_id');
       $onTopPropertyIdArray = TblHome::where('status', 1)->whereIn('id', $Home_id)->get()->pluck('id')->toArray();
        
        
        //$onTopPropertyIdArray = TblHome::where('status', 1)->where('collection_id', '!=', 0)->get()->pluck('id')->toArray();
        $AllPropertyArray = propertyListByLocation($onTopPropertyIdArray, $guestCount);
        
        $sortOrder = $request->get('sort', 'low_to_high');
        usort($AllPropertyArray, function ($a, $b) use ($sortOrder) {
            $priceA = $a->per_night_price * $a->noOfNights;
            $priceB = $b->per_night_price * $b->noOfNights;
    
            if ($sortOrder === 'high_to_low') {
                return $priceB <=> $priceA;
            }
            return $priceA <=> $priceB;
        });
        
        $defaultPerPage = 12;
        $currentPage = $request->get('page', 1);
        $perPage = $defaultPerPage * $currentPage;
        $currentPageItems = array_slice($AllPropertyArray, 0, $perPage);
        $properties = new LengthAwarePaginator(
            $currentPageItems,
            count($AllPropertyArray),
            $defaultPerPage, 
            $currentPage,
            ['path' => url()->current()] 
        );
        $totalStays = count($AllPropertyArray);
        return view('frontend.properties.collections-property-list', [
            'properties' => $properties, 
            'totalStays' => $totalStays,
            'sortOrder' => $sortOrder, 
        ]);
    }
    
    
    public function collectionProperty(Request $request, $slug){
        $guestCount = 1;
        $collection = TblCollection::where('collection_name', $slug)->first();
        
    
        $Home_id = TblHomeCollection::where('collection_id', $collection->id)->get()->pluck('home_id');
        $onTopPropertyIdArray = TblHome::whereIn('id', $Home_id)->where('status', 1)->get()->pluck('id')->toArray();
        
      //  $onTopPropertyIdArray = TblHome::where('collection_id', $collection->id)->where('status', 1)->get()->pluck('id')->toArray();
        $AllPropertyArray = propertyListByLocation($onTopPropertyIdArray, $guestCount);

        $sortOrder = $request->get('sort', 'low_to_high');
        usort($AllPropertyArray, function ($a, $b) use ($sortOrder) {
            $priceA = $a->per_night_price * $a->noOfNights;
            $priceB = $b->per_night_price * $b->noOfNights;
    
            if ($sortOrder === 'high_to_low') {
                return $priceB <=> $priceA;
            }
            return $priceA <=> $priceB;
        });

        $defaultPerPage = 12;
        $currentPage = $request->get('page', 1);
        $perPage = $defaultPerPage * $currentPage;
        $currentPageItems = array_slice($AllPropertyArray, 0, $perPage);
        $properties = new LengthAwarePaginator(
            $currentPageItems,
            count($AllPropertyArray),
            $defaultPerPage, 
            $currentPage,
            ['path' => url()->current()] 
        );
        $totalStays = count($AllPropertyArray);
        return view('frontend.properties.collections-property', 
        compact('properties', 'collection','totalStays','sortOrder'));
    }

    public function locationAllProperty(Request $request, $slug_location){
       
        session()->forget([
            'location_name',
            'checkin_date',
            'checkout_date',
            'city_id',
            'total_guests',
            'adultsCount',
            'childrenCount',
        ]);
        
        $guestCount = 1;
       // session(['total_guests' => $guestCount]);
        $locations = TblLocation::where('location_name', $slug_location)->first();
        $onTopPropertyIdArray = TblHome::where('location_id', $locations->id)->where('status', 1)->get()->pluck('id')->toArray();
        $AllPropertyArray = propertyListByLocation($onTopPropertyIdArray, $guestCount);

        $sortOrder = $request->get('sort', 'low_to_high');
        usort($AllPropertyArray, function ($a, $b) use ($sortOrder) {
            $priceA = $a->per_night_price * $a->noOfNights;
            $priceB = $b->per_night_price * $b->noOfNights;
    
            if ($sortOrder === 'high_to_low') {
                return $priceB <=> $priceA;
            }
            return $priceA <=> $priceB;
        });

        
        $defaultPerPage = 12;
        $currentPage = $request->get('page', 1);
        $perPage = $defaultPerPage * $currentPage;
        $currentPageItems = array_slice($AllPropertyArray, 0, $perPage);
        $properties = new LengthAwarePaginator(
            $currentPageItems,
            count($AllPropertyArray),
            $defaultPerPage, 
            $currentPage,
            ['path' => url()->current()] 
        );
        $totalStays = count($AllPropertyArray);
        
        
        return view('frontend.properties.location-based-property', 
        compact('properties', 'locations','totalStays','sortOrder'));
    }


public function stateAllProperty(Request $request, $state){
    
        session()->forget([
            'location_name',
            'checkin_date',
            'checkout_date',
            'city_id',
            'total_guests',
            'adultsCount',
            'childrenCount',
        ]);
        
        $guestCount = 1;
        //session(['total_guests' => $guestCount]);
        
        $states = TblState::where('name', $state)->first();
        $locations = TblLocation::where('state_id', $states->id)->first();
        $onTopPropertyIdArray = TblHome::where('state_id', $states->id)->where('status', 1)->get()->pluck('id')->toArray();
        $AllPropertyArray = propertyListByLocation($onTopPropertyIdArray, $guestCount);
        
        $sortOrder = $request->get('sort', 'low_to_high');
        usort($AllPropertyArray, function ($a, $b) use ($sortOrder) {
            $priceA = $a->per_night_price * $a->noOfNights;
            $priceB = $b->per_night_price * $b->noOfNights;
    
            if ($sortOrder === 'high_to_low') {
                return $priceB <=> $priceA;
            }
            return $priceA <=> $priceB;
        });
        
        $defaultPerPage = 12;
        $currentPage = $request->get('page', 1);
        $perPage = $defaultPerPage * $currentPage;
        $currentPageItems = array_slice($AllPropertyArray, 0, $perPage);
        $properties = new LengthAwarePaginator(
            $currentPageItems,
            count($AllPropertyArray),
            $defaultPerPage, 
            $currentPage,
            ['path' => url()->current()] 
        );
      
        $totalStays = count($currentPageItems);
        return view('frontend.properties.state-property-list', 
        compact('properties', 'states','totalStays', 'locations','sortOrder'));
    }


    public function TagPropertyList(Request $request, $tag_name){

        $getTag = TblTag::where('tags_name', $tag_name)->first();
        
        $tagIds = TblHomeTags::where('tags_id', $getTag->id)->get()->pluck('home_id')->toArray();

        $guestCount = 1;
        $onTopPropertyIdArray = TblHome::where('status', 1)->get()->pluck('id')->toArray();
        
        $AllPropertyArray = collect(propertyListByLocation($onTopPropertyIdArray, $guestCount));

        $AllPropertyArray = $AllPropertyArray->whereIn('id', $tagIds);

        $finalArray = array();
        foreach($AllPropertyArray as $value){
            array_push($finalArray, $value);
        }
        
        $sortOrder = $request->get('sort', 'low_to_high');
        usort($finalArray, function ($a, $b) use ($sortOrder) {
            $priceA = $a->per_night_price * $a->noOfNights;
            $priceB = $b->per_night_price * $b->noOfNights;
    
            if ($sortOrder === 'high_to_low') {
                return $priceB <=> $priceA;
            }
            return $priceA <=> $priceB;
        });
        
        $defaultPerPage = 12;
        $currentPage = $request->get('page', 1);
        $perPage = $defaultPerPage * $currentPage;
        $currentPageItems = array_slice($finalArray, 0, $perPage);
        $properties = new LengthAwarePaginator(
            $currentPageItems,
            count($finalArray),
            $defaultPerPage, 
            $currentPage,
            ['path' => url()->current()] 
        );
        $totalStays = count($finalArray);
        
        
        return view('frontend.properties.tag-property-list', 
            compact('properties','getTag','totalStays','sortOrder'));
    }
    // public function properties(Request $request){
    //     return view('frontend.properties.properties');
    // }
    
    // public function propertyDetail($slug){
    //  $property = TblHome::with(['images','amenities','homeVideo','homeReviews','homeFeatures','tags'])
    //         ->where('url_key', $slug)
    //         ->where('status', 1)
    //         ->whereNull('deleted_at')
    //         ->first();
    //     $images = TblHomeImageVideo::where('status', 1)
    //         ->whereNull('deleted_at')
    //         ->get();
    
    //     return view('frontend.properties.property-detail', [
    //         'property' => $property,
          
    //     ]);
    // }
    

    public function propertyDetail(Request $request, $home_type, $slug){
        $home_type = strtolower($home_type);
     $property = TblHome::with(['images','amenities','homeVideo','homeReviews','homeFeatures','tags'])
            ->where('url_key', $slug)
            ->orWhere('slug', $slug)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();
            
           
            if (!$property) {
                abort(404);
            }
           /// dd($property->images);
            $checkInDate = date('Y-m-d');
            $propertyCheckInDate = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'yes')->where('availability_date', '>=', date('Y-m-d'))->orderBy('availability_date', 'asc')->first();
            $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();
            if(isset($propertyCheckInDate->availability_date)){
                $checkInDate =  $propertyCheckInDate->availability_date;
            }
            
            $minStayController = new MinStayController();
            $minStay = 1;
            
            
            // $minStay = $minStayController->syncMinStay(date('Y-m-d', strtotime($checkInDate)), $property->id);
            // if($minStay){
            //     $minStay = (integer)$minStay;
            // }
            
            if($request->has('checkin_date') && !empty($request->input('checkin_date'))){
                $minStay = $minStayController->syncMinStay(date('Y-m-d', strtotime($checkInDate)), $property->id);
                
                if($minStay){
                    $minStay = (integer)$minStay;
                }
            }

            
            
            $checkOutDate = date('Y-m-d', strtotime($checkInDate . ' +' . $minStay . ' days'));

            // if(session()->has('checkin_date')){
            //     $checkInDate = session('checkin_date');
            // }
            // if(session()->has('checkout_date')){
            //     $checkOutDate = session('checkout_date');
            // }
            // $adult = 1;
            // $child = 0;
            // if(session()->has('adultsCount')){
            //     $adult = session('adultsCount');
            // }
            // if(session()->has('childrenCount')){
            //     $child = session('childrenCount');
            // }
            $adult = 1;
            $child = 0;
            
            $location_name = $slug = $request->input('location_name');
            $checkin_date = $request->input('checkin_date');
            $checkout_date = $request->input('checkout_date');
            $city_id = $request->input('city_id');
            $total_guests = $request->input('total_guests');
            $adultsCount = $request->input('adultsCount');
            $childrenCount = $request->input('childrenCount');
            //$guestCount = $adultsCount + $childrenCount;
            $guestCount = $adultsCount;
            
            $isBookingEnable = false;
    
            if($request->has('adultsCount') && !empty($request->input('adultsCount'))){
                $adult = $request->input('adultsCount');
            }
            if($request->has('childrenCount') && !empty($request->input('childrenCount'))){
                $child = $request->input('childrenCount');
            }
            
            if($request->has('checkin_date') && !empty($request->input('checkin_date'))){
                $checkInDate = $request->input('checkin_date');
                $isBookingEnable = true;
            }
            if($request->has('checkout_date') && !empty($request->input('checkout_date'))){
                $checkOutDate = $request->input('checkout_date');
            }



           // $totGuest = $adult + $child;
            $totGuest = $adult;
            $noOfGuest = $totGuest;
            $no_of_nights = (integer)MasterHelper::getDateDifference($checkInDate, $checkOutDate);
            $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();
            $minStayController = new MinStayController();
            $minStayArray = $minStayController->syncMinStayFromTo(date('Y-m-d'), $property->id);
            
            $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $property->id)->where('checkout_date', '>=', date('Y-m-d'))->where('property_booking_status', 'Confirmed')->pluck('checkout_date')->toArray();

            $totalReviews = $property->homeReviews->count();
            
            
            

            return view('frontend.properties.property-detail', compact('property', 'no_of_nights', 'totGuest', 'adult', 'child', 'checkInDate', 'checkOutDate', 'propertyUnavailableDates', 'minStayArray', 'previouslyBookedCheckoutDates',
            'location_name', 'checkin_date', 
            'checkout_date', 'city_id', 
            'total_guests', 'adultsCount', 
            'childrenCount', 'guestCount',
            'totalReviews',
            'isBookingEnable'
            ));
}


public function PropertyPriceFilter(Request $request){
    try {
        $property = TblHome::with(['imagesVideos', 'tags', 'homeFeatures', 'homeReviews', 'additionalCharge', 'homeAmenities'])->where('ru_property_id', $request->propertyId)->first();
    
        $noOfGuest =  $request->tot_guest;
        $no_of_nights = $request->tot_no_of_days;
        $checkInDate = $request->checkin_date;
        $checkOutDate = $request->checkout_date;

        $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($checkInDate)), date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))])->sum('price');
        

        if($price ==0){
            $xmlReqForPropertyPrice = "<Pull_ListPropertyPrices_RQ>
                <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                </Authentication>
                <PropertyID>".$property->ru_property_id."</PropertyID>
                <DateFrom>".date('Y-m-d', strtotime($checkInDate))."</DateFrom>
                <DateTo>".date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))."</DateTo>
            </Pull_ListPropertyPrices_RQ>";
            $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);
            $priceListDateWise = $ruPropertyPriceResponse['data']['Prices']['Season'];
            if(date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days')) == date('Y-m-d', strtotime($checkInDate))){
                $price  = $base_price =  $priceListDateWise['Price'];
            }
            else{
                foreach($priceListDateWise as $key=>$value){
                    $price = $base_price= $price + $value['Price'];
                }
            }
        }
        $base_price = $price;
        $price_per_night =  $price/$no_of_nights;
        $base_price =  $base_price/$no_of_nights;
      
        if(setting()->website_markup){   
            $price_per_night = $price_per_night +  ($price_per_night*setting()->website_markup)/100;
        }
        $no_of_nights = (integer)MasterHelper::getDateDifference($checkInDate, $checkOutDate);

        
        
        $price = $base_price =   $base_price_with_other_charges = $price_per_night*$no_of_nights;

      
        $extra_guest_charge = 0;
        if($noOfGuest >$property->guests_included && $noOfGuest <= $property->maximum_number_of_guests){
            if($property->maximum_number_of_guests == $noOfGuest){
                $extra_no_of_guest = $property->maximum_number_of_guests - $property->guests_included;
            }
            else{
                $extra_no_of_guest = $property->maximum_number_of_guests - $noOfGuest;
            }
            $extra_guest_charge = $extra_no_of_guest*$property->extra_guest_charges*$no_of_nights;
            $price = $price + $extra_guest_charge;
            $base_price_with_other_charges = $base_price_with_other_charges + $extra_guest_charge;
        }
        $tax = 12;
        $total_additional_charges =0;
        $additionalCharges = array();
        $additional_charges_name = "";
        if($property->additionalCharge){
            foreach($property->additionalCharge as $akey=>$avalue){
                if($avalue->type_option=='Per_Night'){
                    
                    $price =  $price + $avalue->price*$no_of_nights;
                    $total_additional_charges =  $total_additional_charges + $avalue->price*$no_of_nights;
                    $property->additionalCharge[$akey]->final_additional_charge = $avalue->price*$no_of_nights;
                    $additional_charges_name = $avalue->name;
                }
                else{
                    $price =  $price + $avalue->price;
                    $total_additional_charges =  $total_additional_charges + $avalue->price;
                    $property->additionalCharge[$akey]->final_additional_charge = $avalue->price*$no_of_nights;
                    $additional_charges_name = $avalue->name;
                }
            }
            $additionalCharges = $property->additionalCharge;
        }
        
        $getAppliedGst  = getAppliedGst($price_per_night);
        if($getAppliedGst){
            $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
            $tax_amount = $precentageAmount;
            $tax = $getAppliedGst->gst_percentage;
            $price = $tax_amount + $price;
        }
        
        $total_price_multiple = $price_per_night * $request->tot_no_of_days;
        //$price = 1;
        return response()->json([
            'status' => true,
            'data' => array('base_price'=>round($base_price), 'extra_guest_charge'=>round($property->extra_guest_charges), 'total_extra_guest_charge'=>round($extra_guest_charge), 'total_price'=>round($price), 'tax_amount'=>number_format(round($tax_amount)), 'tax'=>$tax, 'num_formatted_tot_price'=>number_format(round($price)), 
        
        'total_price_multiple'=>number_format(round($total_price_multiple)),
        'additional_charges_name' =>$additional_charges_name,
            'price_per_night'=>$price_per_night, 'price_per_night_num_formatted'=>number_format(round($price_per_night)), 'per_night_price'=>round($price_per_night), 'formatted_base_price'=>number_format(round($base_price)), 'formatted_total_taxable_amount'=>number_format(round($tax_amount)), 'additionalCharges'=>$additionalCharges, 'amountBeforeTax'=>round($base_price_with_other_charges), 
        'total_additional_charges'=>round($total_additional_charges), 'tax_amount'=>$tax_amount, 'tot'=>number_format(round($base_price_with_other_charges) + round($tax_amount) + $total_additional_charges)),
            'message' => 'Listed successfully.'
        ], 200);
    }
    catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => "Internal Error",
            'error' => $e->getMessage(),
        ], 500);
    }
}


// public function applyBookingCouponCode(Request $request)
// {
//     try {
//         $currDate = date('Y-m-d');
//         $checkin_date = date('Y-m-d', strtotime($request->checkin_date));
//         $checkout_date = date('Y-m-d', strtotime($request->checkout_date));
//         $detail = DiscountCouponCodeMapping::with('discountCoupon')->where('code', $request->coupon_code)->first();
    
//         if ($detail) {
//             if ($detail->discountCoupon->status == 1) {
//                 if ($detail->discountCoupon->start_date <= $currDate && $detail->discountCoupon->end_date >= $currDate) {
//                     if ($detail->discountCoupon->stay_date_from && $detail->discountCoupon->stay_date_to) {
//                         if ($detail->discountCoupon->stay_date_from <= $checkin_date && $detail->discountCoupon->stay_date_to >= $checkout_date) {
//                             if ($detail->discountCoupon->property_id) {
//                                 $propertyIds = json_decode($detail->discountCoupon->property_id);
//                                 if (!is_array($propertyIds)) {
//                                     $propertyIds = explode(',', $detail->discountCoupon->property_id);
//                                 }
//                                 if (in_array((int) $request->property_id, $propertyIds)) {
//                                     return response()->json([
//                                         'status' => true,
//                                         'message' => "Success",
//                                         'discount' => $detail->discountCoupon->discount,
//                                         'discount_type' => $detail->discountCoupon->discount_type,
//                                     ], 200);
//                                 } else {
//                                     return response()->json([
//                                         'status' => false,
//                                         'message' => "Invalid Coupon!",
//                                     ], 200);
//                                 }
//                             } else {
//                                 return response()->json([
//                                     'status' => true,
//                                     'message' => "Success",
//                                     'discount' => $detail->discountCoupon->discount,
//                                     'discount_type' => $detail->discountCoupon->discount_type,
//                                 ], 200);
//                             }
                            
//                         } else {
//                             return response()->json([
//                                 'status' => false,
//                                 'message' => "Invalid Coupon!",
//                             ], 200);
//                         }
//                     } else {
//                         $propertyIds = json_decode($detail->discountCoupon->property_id);

//                         if (!is_array($propertyIds)) {
//                             $propertyIds = explode(',', $detail->discountCoupon->property_id);
//                         }
//                         if (in_array((int) $request->property_id, $propertyIds)) {
//                             return response()->json([
//                                 'status' => true,
//                                 'message' => "Success",
//                                 'discount' => $detail->discountCoupon->discount,
//                                 'discount_type' => $detail->discountCoupon->discount_type,
//                             ], 200);
//                         } else {
//                             return response()->json([
//                                 'status' => false,
//                                 'message' => "Invalid Coupon!",
//                             ], 200);
//                         }
//                     }
//                 } else {
//                     return response()->json([
//                         'status' => false,
//                         'message' => "Invalid Coupon!",
//                     ], 200);
//                 }
//             } else {
//                 return response()->json([
//                     'status' => false,
//                     'message' => "Invalid Coupon!",
//                 ], 200);
//             }
//         } else {
//             return response()->json([
//                 'status' => false,
//                 'message' => "Invalid Coupon!",
//             ], 200);
//         }
//     } catch (Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => "Internal Error",
//             'error' => $e->getMessage(),
//         ], 200);
//     }
// }


public function applyBookingCouponCode(Request $request){
    try{
        $currDate = date('Y-m-d');
        $checkin_date = date('Y-m-d', strtotime($request->checkin_date));
        $checkout_date = date('Y-m-d', strtotime($request->checkout_date));
        $detail = DiscountCouponCodeMapping::with('discountCoupon')->where('code', $request->coupon_code)->first();
      
        if($detail){
            if($detail->discountCoupon->status==1){
                if($detail->discountCoupon->start_date <=$currDate && $detail->discountCoupon->end_date >=$currDate){
                    if($detail->discountCoupon->stay_date_from && $detail->discountCoupon->stay_date_to){
                        if($detail->discountCoupon->stay_date_from <=$checkin_date && $detail->discountCoupon->stay_date_to >=$checkout_date){
                            if($detail->discountCoupon->property_id){
                                if(in_array($request->propertyId, $detail->discountCoupon->property_id)){
                                    return response()->json([
                                        'status' => true,
                                        'message' => "Success",
                                        'discount' => $detail->discountCoupon->discount,
                                        'discount_type' => $detail->discountCoupon->discount_type,
                                    ], 200);
                                }
                                else{
                                    return response()->json([
                                        'status' => false,
                                        'message' => "Invalid Coupon!",
                                    ], 200);
                                }
                            }
                            else{
                                return response()->json([
                                    'status' => true,
                                    'message' => "Success",
                                    'discount' => $detail->discountCoupon->discount,
                                    'discount_type' => $detail->discountCoupon->discount_type,
                                ], 200);
                            }
                            
                        }
                        else{
                            return response()->json([
                                'status' => false,
                                'message' => "Invalid Coupon!",
                            ], 200);
                        }
                    }
                    else{
                        if($detail->discountCoupon->property_id){
                            if(in_array($request->propertyId, $detail->discountCoupon->property_id)){
                                return response()->json([
                                    'status' => true,
                                    'message' => "Success",
                                    'discount' => $detail->discountCoupon->discount,
                                    'discount_type' => $detail->discountCoupon->discount_type,
                                ], 200);
                            }
                            else{
                                return response()->json([
                                    'status' => false,
                                    'message' => "Invalid Coupon!",
                                ], 200);
                            }
                        }
                        else{
                            return response()->json([
                                'status' => true,
                                'message' => "Success",
                                'discount' => $detail->discountCoupon->discount,
                                'discount_type' => $detail->discountCoupon->discount_type,
                            ], 200);
                        }
                    }
                }
                else{
                    return response()->json([
                        'status' => false,
                        'message' => "Invalid Coupon!",
                    ], 200);
                }
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => "Invalid Coupon!",
                ], 200);
            }    
        }
        else{
            return response()->json([
                'status' => false,
                'message' => "Invalid Coupon!",
            ], 200);
        }
    }
    catch(Exception $e){
        return response()->json([
            'status' => false,
            'message' => "Internal Error",
            'error' => $e->getMessage(),
        ], 200);
    }
}


   public function bookProperty(Request $request){
        //  $property = TblHome::with('additionalCharge')->where('id', $request->id)->first();
        //  $requestParameters = $request->all();
        //  return view('frontend.booking.property-booking', compact('property','requestParameters'));
        
        $property = TblHome::with('additionalCharge')->where('id', $request->id)->first();
        $requestParameters = $request->all();
        $count = DB::table("ru_property_availabilities")
                            ->where("ru_property_id", $property->ru_property_id)
                            ->where(
                                "availability_date",
                                ">=",
                                date('y-m-d', strtotime($request->ci_date))
                            )
                            ->where(
                                "availability_date",
                                "<=",
                                date('y-m-d', strtotime($request->co_date))
                            )
                            ->where("is_available", "no")
                            ->count();  
                            //$count = 2;
            $slug = $request->input('slug');
            $location_name = $request->input('location_name');
            $checkin_date = $request->input('checkin_date');
            $checkout_date = $request->input('checkout_date');
            $city_id = $request->input('city_id');
            $total_guests = $request->input('total_guests');
            $adultsCount = $request->input('adultsCount', 1); 
            $childrenCount = $request->input('childrenCount', 0); 
            $guestCount = $request->input('guestCount', 1); 
          // $count = 0;
          
            return view('frontend.booking.property-booking', compact('property','requestParameters',
                'slug',
            'location_name', 'checkin_date', 
            'checkout_date', 'city_id', 
            'total_guests', 'adultsCount', 
            'childrenCount', 'guestCount', 
            
            ));

            // if($count == 0){                
              
            // }
            // else{
            //   return back()->with('status', 'The property has already been booked for the selected date range.');
            // }
        
        
    }

    public function locationSessionSave(Request $request){
        $sessionData = [];
        if ($request->has('location_name') && !empty($request->input('location_name'))) {
            $sessionData['location_name'] = $request->input('location_name');
        }
        if ($request->has('city_id') && !empty($request->input('city_id'))) {
            $sessionData['city_id'] = $request->input('city_id');
        }
        if ($request->has('checkin_date') || !empty($request->input('checkin_date'))) {
            $sessionData['checkin_date'] = $request->input('checkin_date');
        }
        if ($request->has('checkout_date') || !empty($request->input('checkout_date'))) {
            $sessionData['checkout_date'] = $request->input('checkout_date');
        }
        if ($request->has('adultsCount') && !empty($request->input('adultsCount'))) {
            $sessionData['adultsCount'] = $request->input('adultsCount');
        }
        if ($request->has('childrenCount') && !empty($request->input('childrenCount')) || $request->input('childrenCount') ==0) {
            $sessionData['childrenCount'] = $request->input('childrenCount');
        }
        if ($request->has('total_guests') && !empty($request->input('total_guests'))) {
            $sessionData['total_guests'] = $request->input('total_guests');
        }
        if (!empty($sessionData)) {
            session($sessionData);  
        }
        return response()->json([
            'status' => true,
            'message' => 'Data saved successfully.'
        ], 200);
    }

    // public function PropertyList(Request $request, $slug=null){

    //     $location_name = $slug = $request->input('location_name');
    //     $checkin_date = $request->input('checkin_date');
    //     $checkout_date = $request->input('checkout_date');
    //     $city_id = $request->input('city_id');
    //     $total_guests = $request->input('total_guests');
    //     $adultsCount = $request->input('adultsCount');
    //     $childrenCount = $request->input('childrenCount');
    //     $guestCount = $adultsCount + $childrenCount;
        
        
    //     $locations = TblLocation::where('location_name', $slug)->first();
    //     if($slug){
    //         $onTopPropertyIdArray = TblHome::with('amenities') 
    // ->where('location_id', $locations->id)
    // ->where('status', 1)
    // ->get() 
    // ->pluck('id') 
    // ->toArray(); 

    //     }
    //     else{
    //         $onTopPropertyIdArray = TblHome::where('status', 1)->get()->pluck('id')->toArray();
    //     }

    //     $AllPropertyArray = propertyListByBasedLocation($onTopPropertyIdArray, $guestCount);
         
    //     if(($checkin_date === null)){
    //         $AllPropertyArray = propertyListByBasedLocation($onTopPropertyIdArray, $total_guests);
    //     }
    //     else{
    //         $checkin_date_formatted = DateTime::createFromFormat('jS M Y', $checkin_date)->format('Y-m-d');
    //         $checkout_date_formatted = DateTime::createFromFormat('jS M Y', $checkout_date)->format('Y-m-d');
    //         $search_parameters = array('arrivalDate'=>date('Y-m-d', strtotime($checkin_date_formatted)), 'departureDate'=>date('Y-m-d', strtotime($checkout_date_formatted)), 'guest_count'=>$total_guests, 'location_id'=>$city_id);
    //         $AllPropertyArray = propertyList($search_parameters);
          
           
    //     }
        
    //     $sortOrder = $request->get('sort', 'low_to_high');
    //     usort($AllPropertyArray, function ($a, $b) use ($sortOrder) {
    //         $priceA = $a->per_night_price * $a->noOfNights;
    //         $priceB = $b->per_night_price * $b->noOfNights;
    
    //         if ($sortOrder === 'high_to_low') {
    //             return $priceB <=> $priceA;
    //         }
    //         return $priceA <=> $priceB;
    //     });
        
    //     $defaultPerPage = 12;
    //     $currentPage = $request->get('page', 1);
    //     $perPage = $defaultPerPage * $currentPage;
    //     $currentPageItems = array_slice($AllPropertyArray, 0, $perPage);
    //     $properties = new LengthAwarePaginator(
    //         $currentPageItems,
    //         count($AllPropertyArray),
    //         $defaultPerPage, 
    //         $currentPage,
    //         ['path' => url()->current()] 
    //     );
    //     $totalStays = count($AllPropertyArray);
    //     return view('frontend.properties.property-list',compact('properties','sortOrder','locations','totalStays', 'checkin_date', 'checkout_date', 'guestCount',
    //     'location_name', 'checkin_date', 
    //     'checkout_date', 'city_id', 
    //     'total_guests', 'adultsCount', 
    //     'childrenCount', 'guestCount', 
        
    //     ));



    // }


public function PropertyList(Request $request, $slug = null)
{
    $type = $request->input('type');
   // dd($request);
    switch ($type) {
        case "listAllProperty":
            return $this->listAllProperty($request);
        case "listCollectionProperty":
            return $this->listCollectionProperty($request);
        case "listCollectionBasedProperty":
            return $this->listCollectionBasedProperty($request);
        case "listPropertiesSearch":
            return $this->listPropertiesSearch($request);
        case "listpropertyType":
            return $this->listpropertyType($request);
        case "listTagBasedProperty":
                return $this->listTagBasedProperty($request);
        default:
        abort(404);
    }
}

private function getRequestParams(Request $request)
{
    return [
        'location_name' => $request->input('location_name'),
        'tag_name' => $request->input('tag_name'),
        'filter_type' => $request->input('filter_type'),
        'checkin_date' => $request->input('checkin_date'),
        'checkout_date' => $request->input('checkout_date'),
        'city_id' => $request->input('city_id'),
        'total_guests' => $request->input('total_guests'),
        'adultsCount' => $request->input('adultsCount', 1),
        'childrenCount' => $request->input('childrenCount', 0),
        'guestCount' => max($request->input('adultsCount', 1) + $request->input('childrenCount', 0), 1),
        'sortOrder' => $request->get('sort', 'low_to_high'),
        'page' => $request->get('page', 1),
        'defaultPerPage' => 12,
        'sort_by' => $request->input('sort_by'),
    ];
}

private function listAllProperty(Request $request){
    $params = $this->getRequestParams($request);
    $guestCount = 1;

    $query = TblHome::where('status', 1);
    if ($request->has('property_type') && $request->property_type !== 'propertyType') {
        $selectedTypes = explode(',', $request->input('property_type')); 
        $homeType_id = TblHomeType::whereIn('url_key', $selectedTypes)->get()->pluck('id')->toArray();
        $query->whereIn('home_type_id', $homeType_id);
    }

     if ($request->has('location') && $request->location !== 'all') {
        $selectedLocations = explode(',', $request->input('location'));
        $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
        $query->whereIn('location_id', $locations);
      }

    // // **Filter by Price Range**
    //  if ($request->has('min_price') && $request->has('max_price')) {
    //     $query->whereBetween('per_night_price', [$request->min_price, $request->max_price]);
    //  }

    if ($request->has('rooms') && $request->rooms > 0) {
        $query->where('no_of_bedrooms', '>=', $request->rooms);
    }

    if ($request->has('amenities')) {
        $amenities = explode(',', $request->input('amenities'));
        $query->whereHas('amenities', function ($q) use ($amenities) {
            $q->whereIn('amenities_id', $amenities);
        });
    }

    $onTopPropertyIdArray = $query->pluck('id')->toArray();
  //  $AllPropertyArray = propertyListByLocationFilter($onTopPropertyIdArray, $guestCount);

  $min_price = $request->has('min_price') ? (int)$request->min_price : null;
  $max_price = $request->has('max_price') ? (int)$request->max_price : null;
  
  $AllPropertyArray = propertyListByLocationFilter($onTopPropertyIdArray, $guestCount, $min_price, $max_price);

    // $AllPropertyArray = collect($AllPropertyArray)->sortBy(
    //     fn($property) => $params['sort_by'] === 'high_to_low' ? -$property->per_night_price : $property->per_night_price
    // )->values()->all();
    
    $sortBy = $params['sort_by'] ?? 'low_to_high'; // Default to "low_to_high"
    $AllPropertyArray = collect($AllPropertyArray)->sortBy(
        fn($property) => $sortBy === 'low_to_high' ? $property->per_night_price : -$property->per_night_price
    )->values()->all();
   
    

    $totalStays = count($AllPropertyArray);
   
    $properties = $this->paginateArray($AllPropertyArray, $params['page'], $params['defaultPerPage']);

   


    $propertyCount = count($properties);
    return view('frontend.properties.property-list', array_merge($params, compact('properties', 'totalStays','propertyCount')));
}




private function listCollectionProperty(Request $request)
{
    $params = $this->getRequestParams($request);
    $guestCount = 1;
    $query = TblHome::where('status', 1);
    if ($request->has('property_type') && $request->property_type !== 'propertyType') {
        $selectedTypes = explode(',', $request->input('property_type')); 
        $homeType_id = TblHomeType::whereIn('url_key', $selectedTypes)->get()->pluck('id')->toArray();
        $query->whereIn('home_type_id', $homeType_id);
    }
    if ($request->has('location') && $request->location !== 'all') {
        $selectedLocations = explode(',', $request->input('location'));
        $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
        $query->whereIn('location_id', $locations);
    }
    if ($request->has('amenities')) {
        $selectedAmenities = explode(',', $request->input('amenities'));
        $query->whereHas('homeAmenities', function ($q) use ($selectedAmenities) {
            $q->whereIn('amenities_id', $selectedAmenities);
        });
    }
    if ($request->has('rooms')) {
        $query->where('no_of_bedrooms', '>=', $request->input('rooms'));
    }
    // if ($request->has('min_price') && $request->has('max_price')) {
    //     $query->whereBetween('per_night_price', [$request->input('min_price'), $request->input('max_price')]);
    // }
    $collections = TblCollection::pluck('id');
    $homeIds = TblHomeCollection::whereIn('collection_id', $collections)->pluck('home_id');
    $query->whereIn('id', $homeIds);
    $onTopPropertyIdArray = $query->pluck('id')->toArray();
    //$AllPropertyArray = propertyListByLocation($onTopPropertyIdArray, $guestCount);

    $min_price = $request->has('min_price') ? (int)$request->min_price : null;
    $max_price = $request->has('max_price') ? (int)$request->max_price : null; 
    $AllPropertyArray = propertyListByLocationFilter($onTopPropertyIdArray, $guestCount, $min_price, $max_price);


    // $AllPropertyArray = collect($AllPropertyArray)->sortBy(
    //     fn($property) => $params['sortOrder'] === 'high_to_low' ? -$property->per_night_price : $property->per_night_price
    // )->values()->all();
    
    $sortBy = $params['sort_by'] ?? 'low_to_high'; // Default to "low_to_high"
    $AllPropertyArray = collect($AllPropertyArray)->sortBy(
        fn($property) => $sortBy === 'low_to_high' ? $property->per_night_price : -$property->per_night_price
    )->values()->all();

    $totalStays = count($AllPropertyArray);
    $properties = $this->paginateArray($AllPropertyArray, $params['page'], $params['defaultPerPage']);
    $propertyCount = count($properties);
    return view('frontend.properties.property-list', array_merge($params, compact('properties', 'totalStays','propertyCount')));
}




private function listCollectionBasedProperty(Request $request)
{
    $params = $this->getRequestParams($request);
    $guestCount = 1;

    $query = TblHome::where('status', 1);
    if ($request->has('property_type') && $request->property_type !== 'propertyType') {
        $selectedTypes = explode(',', $request->input('property_type')); 
        $homeType_id = TblHomeType::whereIn('url_key', $selectedTypes)->get()->pluck('id')->toArray();
        $query->whereIn('home_type_id', $homeType_id);
    }

    if ($request->has('location') && $request->location !== 'all') {
        $selectedLocations = explode(',', $request->input('location'));
        $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
        $query->whereIn('location_id', $locations);
    }
    if ($request->has('amenities')) {
        $selectedAmenities = explode(',', $request->input('amenities'));
        $query->whereHas('homeAmenities', function ($q) use ($selectedAmenities) {
            $q->whereIn('amenities_id', $selectedAmenities);
        });
    }
    if ($request->has('rooms')) {
        $query->where('no_of_bedrooms', '>=', $request->input('rooms'));
    }

   
    $collection = TblCollection::where('collection_name', $request->filter_name)->first();
    if (!$collection) {
        abort(404);
    }
    $homeIdsFromCollection = TblHomeCollection::where('collection_id', $collection->id)->pluck('home_id');

    $query->whereIn('id', $homeIdsFromCollection);
    $filteredProperties = $query->get();

    $filteredPropertyIds = $filteredProperties->pluck('id')->toArray();
   // $AllPropertyArray = propertyListByLocation($filteredPropertyIds, $guestCount);

   $min_price = $request->has('min_price') ? (int)$request->min_price : null;
   $max_price = $request->has('max_price') ? (int)$request->max_price : null; 
   $AllPropertyArray = propertyListByLocationFilter($filteredPropertyIds, $guestCount, $min_price, $max_price);


    // $AllPropertyArray = collect($AllPropertyArray)->sortBy(
    //     fn($property) => $params['sortOrder'] === 'high_to_low' ? -$property->per_night_price : $property->per_night_price
    // )->values()->all();
    
    $sortBy = $params['sort_by'] ?? 'low_to_high'; // Default to "low_to_high"
    $AllPropertyArray = collect($AllPropertyArray)->sortBy(
        fn($property) => $sortBy === 'low_to_high' ? $property->per_night_price : -$property->per_night_price
    )->values()->all();
    

    $totalStays = count($AllPropertyArray);
    $properties = $this->paginateArray($AllPropertyArray, $params['page'], $params['defaultPerPage']);
    $propertyCount = count($properties);
    return view('frontend.properties.property-list', array_merge($params, compact('properties', 'totalStays','propertyCount')));
}

private function listTagBasedProperty(Request $request)
{
    $params = $this->getRequestParams($request);
    $guestCount = 1;

    $query = TblHome::where('status', 1);
    if ($request->has('property_type') && $request->property_type !== 'propertyType') {
        $selectedTypes = explode(',', $request->input('property_type')); 
        $homeType_id = TblHomeType::whereIn('url_key', $selectedTypes)->get()->pluck('id')->toArray();
        $query->whereIn('home_type_id', $homeType_id);
    }

    if ($request->has('location') && $request->location !== 'all') {
        $selectedLocations = explode(',', $request->input('location'));
        $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
        $query->whereIn('location_id', $locations);
    }
    if ($request->has('amenities')) {
        $selectedAmenities = explode(',', $request->input('amenities'));
        $query->whereHas('homeAmenities', function ($q) use ($selectedAmenities) {
            $q->whereIn('amenities_id', $selectedAmenities);
        });
    }
    if ($request->has('rooms')) {
        $query->where('no_of_bedrooms', '>=', $request->input('rooms'));
    }

    // if ($request->has('min_price') && $request->has('max_price')) {
    //     $query->whereBetween('per_night_price', [$request->input('min_price'), $request->input('max_price')]);
    // }
    $getTag = TblTag::where('tags_name', $request->tag_name)->first();
    if (!$getTag) {
        abort(404);
    }
    
    $tagIds = TblHomeTags::where('tags_id', $getTag->id)->get()->pluck('home_id')->toArray();
    $query->whereIn('id', $tagIds);
    $filteredProperties = $query->get();

    $filteredPropertyIds = $filteredProperties->pluck('id')->toArray();
   // $AllPropertyArray = propertyListByLocation($filteredPropertyIds, $guestCount);

    $min_price = $request->has('min_price') ? (int)$request->min_price : null;
    $max_price = $request->has('max_price') ? (int)$request->max_price : null; 
    $AllPropertyArray = propertyListByLocationFilter($filteredPropertyIds, $guestCount, $min_price, $max_price);


    // $AllPropertyArray = collect($AllPropertyArray)->sortBy(
    //     fn($property) => $params['sortOrder'] === 'high_to_low' ? -$property->per_night_price : $property->per_night_price
    // )->values()->all();

    $sortBy = $params['sort_by'] ?? 'low_to_high'; // Default to "low_to_high"
    $AllPropertyArray = collect($AllPropertyArray)->sortBy(
        fn($property) => $sortBy === 'low_to_high' ? $property->per_night_price : -$property->per_night_price
    )->values()->all();

    $totalStays = count($AllPropertyArray);
    $properties = $this->paginateArray($AllPropertyArray, $params['page'], $params['defaultPerPage']);
    $propertyCount = count($properties);
    return view('frontend.properties.property-list', array_merge($params, compact('properties', 'totalStays','propertyCount')));
}

private function listPropertiesSearch(Request $request)
{
    $params = $this->getRequestParams($request);
   
    $guestCount = $params['total_guests'] ?? 1;

    $query = TblHome::where('status', 1);

    $onTopPropertyIdArray = [];

    if ($request->filter_type == 'location' && $request->has('location') && $request->location !== 'all') {
        $selectedLocations = explode(',', $request->input('location'));
        $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
        $query->whereIn('location_id', $locations);
        $onTopPropertyIdArray = $query->pluck('id')->toArray();
    }

    if ($request->filter_type == 'state') {
      //  $states = TblState::where('name', $request->input('location'))->first();
      //  $query->where('state_id', $states->id);
      //  $onTopPropertyIdArray = $query->pluck('id')->toArray();
      
      $states = TblState::where('name', $request->input('location_name'))->first();
        $selectedLocations = explode(',', $request->input('location'));
        if ($request->has('location') && !empty($request->input('location'))) {
            $locations = TblLocation::where('state_id', $states->id)
                ->whereIn('location_name', $selectedLocations)
                ->pluck('id')
                ->toArray();
        } else {
            $locations = TblLocation::where('state_id', $states->id)
                ->pluck('id')
                ->toArray();
        }

        if (!empty($locations)) {
            $query->whereIn('location_id', $locations);
        }
        $onTopPropertyIdArray = $query->pluck('id')->toArray();
      
    }
    
    if ($request->has('property_type') && $request->property_type !== 'propertyType') {
        $selectedTypes = explode(',', $request->input('property_type')); 
        $homeType_id = TblHomeType::whereIn('url_key', $selectedTypes)->get()->pluck('id')->toArray();
        $query->whereIn('home_type_id', $homeType_id);
        $onTopPropertyIdArray = $query->pluck('id')->toArray();
    }
    if ($request->has('amenities')) {
        $selectedAmenities = explode(',', $request->input('amenities'));
        $query->whereHas('homeAmenities', function ($q) use ($selectedAmenities) {
            $q->whereIn('amenities_id', $selectedAmenities);
        });
        $onTopPropertyIdArray = $query->pluck('id')->toArray();
    }
    if ($request->has('rooms')) {
        $query->where('no_of_bedrooms', '>=', $request->input('rooms'));
        $onTopPropertyIdArray = $query->pluck('id')->toArray();
    }

  
    if ($request->checkin_date && $request->checkout_date) {
        
        $checkin_date_formatted = DateTime::createFromFormat('jS M Y', $params['checkin_date'])->format('Y-m-d');
        $checkout_date_formatted = DateTime::createFromFormat('jS M Y', $params['checkout_date'])->format('Y-m-d');

        if ($request->filter_type == 'location') {
            $locations = TblLocation::where('location_name', $params['location_name'])->first();
            if ($locations) {
                $location_id = $locations->id;
                $search_parameters = [
                    'arrivalDate'   => $checkin_date_formatted,
                    'departureDate' => $checkout_date_formatted,
                    'guest_count'   => $params['total_guests'],
                    'location_id'   => $location_id,
                ];
                $AllPropertyArray = propertyList($search_parameters);
                
              
            }
        }
        else if ($request->filter_type == 'state') {
            $state = TblState::where('name', 'like', $request->location)->first();
            $locations = TblLocation::where('state_id', $state->id)->get()->pluck('id')->toArray();
        
            if ($locations) {
              
                $search_parameters = [
                    'arrivalDate'   => $checkin_date_formatted,
                    'departureDate' => $checkout_date_formatted,
                    'guest_count'   => $params['total_guests'],
                    'locations'   => $locations,
                ];
                $AllPropertyArray = propertyListByState($search_parameters);
               
            }
        }
        else {
            // $query->whereHas('availability', function ($q) use ($checkin_date_formatted, $checkout_date_formatted) {
            //     $q->whereDate('available_from', '<=', $checkin_date_formatted)
            //       ->whereDate('available_to', '>=', $checkout_date_formatted);
            // });
         
            $onTopPropertyIdArray = $query->pluck('id')->toArray();
           // $AllPropertyArray = propertyListByLocation($onTopPropertyIdArray, $guestCount);
            $min_price = $request->has('min_price') ? (int)$request->min_price : null;
            $max_price = $request->has('max_price') ? (int)$request->max_price : null; 
            $AllPropertyArray = propertyListByLocationFilter($onTopPropertyIdArray, $guestCount, $min_price, $max_price);
            
             dd($AllPropertyArray);
        }
    } else {
        // 🔹 If no date filter, fetch properties normally
        $onTopPropertyIdArray = $query->pluck('id')->toArray();
        //$AllPropertyArray = propertyListByLocation($onTopPropertyIdArray, $guestCount);
        $min_price = $request->has('min_price') ? (int)$request->min_price : null;
        $max_price = $request->has('max_price') ? (int)$request->max_price : null; 
        $AllPropertyArray = propertyListByLocationFilter($onTopPropertyIdArray, $guestCount, $min_price, $max_price);
    }

    // 🔹 Sort Properties by Price (High-to-Low or Low-to-High)
    $AllPropertyArray = collect($AllPropertyArray)->sortBy(
        fn($property) => $params['sortOrder'] === 'high_to_low' ? -$property->per_night_price : $property->per_night_price
    )->values()->all();

    // 🔹 Get Total Properties Count
    $totalStays = count($AllPropertyArray);

    // 🔹 Paginate Results
    $properties = $this->paginateArray($AllPropertyArray, $params['page'], $params['defaultPerPage']);
    $propertyCount = count($properties);
    // 🔹 Return View with Filtered Properties
    return view('frontend.properties.property-list', array_merge($params, compact('properties', 'totalStays','propertyCount')));
}







private function paginateArray($items, $currentPage, $perPage)
{
    return new LengthAwarePaginator(
        array_slice($items, ($currentPage - 1) * $perPage, $perPage),
       //array_slice($items->toArray(), ($currentPage - 1) * $perPage, $perPage),  // Convert Collection to Array
        count($items),
        $perPage,
        $currentPage,
        ['path' => url()->current()]
    );
}


// function ajaxFilterProperties(Request $request){
        
//         $query = TblHome::query();
//         if ($request->has('property_type') && $request->property_type !== 'propertyType') {
//           $selectedTypes = explode(',', $request->input('property_type')); 
//           $homeType_id = TblHomeType::whereIn('url_key', $selectedTypes)->get()->pluck('id')->toArray();
//           $query->whereIn('home_type_id', $homeType_id);
//         }
    

//         if ($request->has('location') && $request->location !== 'all' && empty($request->filter_type)) {
//             $selectedLocations = explode(',', $request->input('location'));
//             $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
//             $query->whereIn('location_id', $locations);
          
//         }

        

        
//         if ($request->has('amenities')) {
//             $selectedAmenities = explode(',', $request->input('amenities'));
//             $query->whereHas('homeAmenities', function ($q) use ($selectedAmenities) {
//                 $q->whereIn('amenities_id', $selectedAmenities);
//             });
//         }

       
//         if ($request->has('rooms')) {
//             $query->where('no_of_bedrooms', '>=', $request->input('rooms'));
//         }

        
//         if ($request->has('tag_name')) {
//             $getTag = TblTag::where('tags_name', $request->tag_name)->first();
//             if (!$getTag) {
//                 abort(404);
//             }
//             $tagIds = TblHomeTags::where('tags_id', $getTag->id)->get()->pluck('home_id')->toArray();
//             $query->whereIn('id', $tagIds);
//         }
        
        
//         if ($request->has('type') && $request->input('type') == 'listCollectionProperty') {
//             $collections = TblCollection::pluck('id');
//             $homeIds = TblHomeCollection::whereIn('collection_id', $collections)->pluck('home_id');
//             $query->whereIn('id', $homeIds);
//         }

       
      
//         if($request->has('filter_name') && $request->has('type') && $request->has('type') == 'listCollectionBasedProperty'){
//             $collection = TblCollection::where('collection_name', $request->filter_name)->first();
//             $homeIdsFromCollection = TblHomeCollection::where('collection_id', $collection->id)->pluck('home_id');
//             $query->whereIn('id', $homeIdsFromCollection);
//         }
        
        
//         if ($request->filter_type == 'location' && $request->has('location') && $request->location !== 'all') {
           
//             $selectedLocations = explode(',', $request->input('location'));
//             $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
//             $query->whereIn('location_id', $locations);
//         }
        
        

//         if ($request->filter_type == 'state') {
          
//           // $states = TblState::where('name', $request->input('location'))->first();
//           // $query->where('state_id', $states->id);
           
//           $states = TblState::where('name', $request->input('location_name'))->first();
//             $selectedLocations = explode(',', $request->input('location'));
//             if ($request->has('location') && !empty($request->input('location'))) {
//                 $locations = TblLocation::where('state_id', $states->id)
//                     ->whereIn('location_name', $selectedLocations)
//                     ->pluck('id')
//                     ->toArray();
//             } else {
//                 $locations = TblLocation::where('state_id', $states->id)
//                     ->pluck('id')
//                     ->toArray();
//             }
//             $query->whereIn('location_id', $locations);
           
           
//         }

//         $list = $query->with(['images', 'locationData','tags'])->where('status', 1)->whereNull('deleted_at')->get();

       
       

        

//         $properties = [];
//         foreach ($list as $property) {
//             $price = 0;
//             $minStayController = new MinStayController();
//             $minStay = 1;
//             if($request->checkin_date !='' && $request->checkout_date !=''){
//                 $checkin_date = date('Y-m-d', strtotime($request->checkin_date));
//                 $checkout_date = date('Y-m-d', strtotime($request->checkout_date));
//                 $last_date =  date('Y-m-d', strtotime($checkout_date. '-1 days'));
//                 if($last_date == $checkin_date){
//                     $date_difference_count = 1;
//                 }
//                 else{
//                     $date_difference_count = MasterHelper::getDateDifference($checkin_date, $checkout_date);
//                 }
                
//                 $checkInDate = date('Y-m-d', strtotime($request->checkin_date));
//                 $checkOutDate = date('Y-m-d', strtotime($request->checkout_date));
                
//                 $count = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $checkout_date)->where('is_available', 'no')->count();
              
//                 //$price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [$checkin_date, $last_date])->sum('price');
//               // $price = round($price/$date_difference_count);
//             }
//             else{
            
//                 $propertyCheckInDate = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'yes')->where('availability_date', '>=', date('Y-m-d'))->orderBy('availability_date', 'asc')->first();
                
//                 if(isset($propertyCheckInDate->availability_date)){
//                     $checkInDate =  $propertyCheckInDate->availability_date;
//                 }
//                 else{
//                     $checkInDate =  date('Y-m-d'); 
//                 }
                
                
//             }
//             $minStays = $minStayController->syncMinStay(date('Y-m-d', strtotime($checkInDate)), $property->id);
                
//             if($minStays){
//                 $minStay = (integer)$minStays;
//             }
//             $checkOutDate = date('Y-m-d', strtotime($checkInDate . ' +' . $minStay . ' days'));
//             $last_date =  date('Y-m-d', strtotime($checkOutDate. '-1 days'));
//             if($last_date ==$checkInDate){
//                 $date_difference_count = 1;
//             }
//             else{
//                 $date_difference_count = MasterHelper::getDateDifference($checkInDate, $checkOutDate);
//             }
            
//             $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($checkInDate)),date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))])->sum('price');
//             $price = $price/$date_difference_count;
           
          
//             if($price > 0 ){
//                 if(setting()->website_markup){   
//                     $price = $price +  ($price*setting()->website_markup)/100;
//                 }
//                 $pricea[] = $price;
//                 $property->pl_price = $price;
                
//                 if($price >= (integer)$request->minPrice){
//                     $properties[] = $property;
//                 }
//             }
//         }
//         $properties = collect($properties);
//         if($request->has('min_price') && $request->has('max_price')){
//             $properties = $properties->filter(function ($property) use ($request) {
//                 return $property['pl_price'] >= (integer)$request->min_price && $property['pl_price'] <= (integer)$request->max_price;
//             })->values();
//         }
      
//         // if($request->sort_by === 'low_to_high') {
//         //     $properties = $properties->sortBy('pl_price')->values();
//         // }
//         // else{
//         //     $properties = $properties->sortByDesc('pl_price')->values();
//         // }
        
//         $sortBy = $request->sort_by ?? 'low_to_high'; // Default to "low_to_high"
//         $properties = ($sortBy === 'low_to_high') 
//         ? $properties->sortBy('pl_price')->values() 
//         : $properties->sortByDesc('pl_price')->values();
        
        
        
//         $defaultPerPage = 12;  
//         $currentPage = $request->get('page', 1);
        
//         // Increase the number of results dynamically (e.g., Page 2 = 24, Page 3 = 36, etc.)
//         $itemsToShow = $defaultPerPage * $currentPage;
        
//         $total = $properties->count();
//         $paginatedProperties = $properties->slice(0, $itemsToShow)->values();
        
//         $paginator = new LengthAwarePaginator(
//             $paginatedProperties, 
//             $total, 
//             $itemsToShow, // This increases with each page
//             $currentPage, 
//             ['path' => $request->url()]
//         );
        
//         // Check if more pages exist
//         $nextPage = $itemsToShow < $total ? $currentPage + 1 : null;


        
//         $checkin_date = $request->input('checkin_date');
//         $checkout_date = $request->input('checkout_date');
//         $total_guests = $request->input('total_guests');
//         $adultsCount = $request->input('adultsCount');
//         $childrenCount = $request->input('childrenCount');
//         $guestCount = $adultsCount + $childrenCount;
        
//         $html = view('frontend.properties.ajax-filter-property', ['properties' => $paginator,
        
//         'location_name' => "",
//         'checkin_date' => $checkin_date,
//         'checkout_date' => $checkout_date,
//         'city_id' => $request->location,
//         'total_guests' => $total_guests,
//         'adultsCount' => $adultsCount,
//         'childrenCount' => $childrenCount,
//         'guestCount' => $guestCount,
        
        
//         'nextPage' => $nextPage, 'locationName'=>''])->render();
    
//         return response()->json([
//             'html' => $html,
//             'location_name' => '',
//             'type'=>$request->type,
//             'properties' => $paginator,
//             'filterWithSorting'=>$request->filterWithSorting,
//             'propertyCount'=>$paginator->count(),
//             'total'=>$total,
//             'nextPage' => $nextPage,
//         ]);
//     }

// start

function ajaxFilterProperties(Request $request){
        
        $query = TblHome::query();
        if ($request->has('property_type') && $request->property_type !== 'propertyType') {
          $selectedTypes = explode(',', $request->input('property_type')); 
          $homeType_id = TblHomeType::whereIn('url_key', $selectedTypes)->get()->pluck('id')->toArray();
          $query->whereIn('home_type_id', $homeType_id);
        }
    

        if ($request->has('location') && $request->location !== 'all' && empty($request->filter_type)) {
            $selectedLocations = explode(',', $request->input('location'));
            $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
            $query->whereIn('location_id', $locations);
          
        }

        

        
        if ($request->has('amenities')) {
            $selectedAmenities = explode(',', $request->input('amenities'));
            $query->whereHas('homeAmenities', function ($q) use ($selectedAmenities) {
                $q->whereIn('amenities_id', $selectedAmenities);
            });
        }

       
        if ($request->has('rooms')) {
            $query->where('no_of_bedrooms', '>=', $request->input('rooms'));
        }

        
        if ($request->has('tag_name')) {
            $getTag = TblTag::where('tags_name', $request->tag_name)->first();
            if (!$getTag) {
                abort(404);
            }
            $tagIds = TblHomeTags::where('tags_id', $getTag->id)->get()->pluck('home_id')->toArray();
            $query->whereIn('id', $tagIds);
        }
        
        
        if ($request->has('type') && $request->input('type') == 'listCollectionProperty') {
            $collections = TblCollection::pluck('id');
            $homeIds = TblHomeCollection::whereIn('collection_id', $collections)->pluck('home_id');
            $query->whereIn('id', $homeIds);
        }

       
      
        if($request->has('filter_name') && $request->has('type') && $request->has('type') == 'listCollectionBasedProperty'){
            $collection = TblCollection::where('collection_name', $request->filter_name)->first();
            $homeIdsFromCollection = TblHomeCollection::where('collection_id', $collection->id)->pluck('home_id');
            $query->whereIn('id', $homeIdsFromCollection);
        }
        
        
        if ($request->filter_type == 'location' && $request->has('location') && $request->location !== 'all') {
           
            $selectedLocations = explode(',', $request->input('location'));
            $locations = TblLocation::whereIn('location_name', $selectedLocations)->get()->pluck('id')->toArray();
            $query->whereIn('location_id', $locations);
        }
        
        

        if ($request->filter_type == 'state') {
          
           // $states = TblState::where('name', $request->input('location'))->first();
           // $query->where('state_id', $states->id);
           
           $states = TblState::where('name', $request->input('location_name'))->first();
            $selectedLocations = explode(',', $request->input('location'));
            if ($request->has('location') && !empty($request->input('location'))) {
                $locations = TblLocation::where('state_id', $states->id)
                    ->whereIn('location_name', $selectedLocations)
                    ->pluck('id')
                    ->toArray();
            } else {
                $locations = TblLocation::where('state_id', $states->id)
                    ->pluck('id')
                    ->toArray();
            }
            $query->whereIn('location_id', $locations);
           
           
        }

        $list = $query->with(['images', 'locationData','tags'])->where('status', 1)->whereNull('deleted_at')->get();

       
       

        

        $properties = [];
        foreach ($list as $property) {
            $price = 0;
            $minStayController = new MinStayController();
            $minStay = 1;
            if($request->checkin_date !='' && $request->checkout_date !=''){
                $checkin_date = date('Y-m-d', strtotime($request->checkin_date));
                $checkout_date = date('Y-m-d', strtotime($request->checkout_date));
                $last_date =  date('Y-m-d', strtotime($checkout_date. '-1 days'));
                if($last_date == $checkin_date){
                    $date_difference_count = 1;
                }
                else{
                    $date_difference_count = MasterHelper::getDateDifference($checkin_date, $checkout_date);
                }
                
                $checkInDate = date('Y-m-d', strtotime($request->checkin_date));
                $checkOutDate = date('Y-m-d', strtotime($request->checkout_date));
                
                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $checkout_date)->where('is_available', 'no')->count();
              
                //$price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [$checkin_date, $last_date])->sum('price');
               // $price = round($price/$date_difference_count);

                // dd($checkInDate);
                $minStays = $minStayController->syncMinStay(date('Y-m-d', strtotime($checkInDate)), $property->id);
                // $minStays =  $minStayController->syncMinStay($checkInDate, $property->id); 
                    
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
                $price = $price/$date_difference_count;
                
                  
                if($price > 0 && $count == 0 ){
                    if(setting()->website_markup){   
                        $price = $price +  ($price*setting()->website_markup)/100;
                    }
                    $pricea[] = $price;
                    $property->pl_price = $price;
                    
                    if($price >= (integer)$request->minPrice  ){
                        $properties[] = $property;
                    }
                }
            }
            else{
            
                $propertyCheckInDate = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'yes')->where('availability_date', '>=', date('Y-m-d'))->orderBy('availability_date', 'asc')->first();
                
                if(isset($propertyCheckInDate->availability_date)){
                    $checkInDate =  $propertyCheckInDate->availability_date;
                }
                else{
                    $checkInDate =  date('Y-m-d'); 
                }

                 // dd($checkInDate);
                $minStays = $minStayController->syncMinStay(date('Y-m-d', strtotime($checkInDate)), $property->id);
                // $minStays =  $minStayController->syncMinStay($checkInDate, $property->id); 
                    
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
                $price = $price/$date_difference_count;
                
            
                if($price > 0 ){
                    if(setting()->website_markup){   
                        $price = $price +  ($price*setting()->website_markup)/100;
                    }
                    $pricea[] = $price;
                    $property->pl_price = $price;
                    
                    if($price >= (integer)$request->minPrice){
                        $properties[] = $property;
                    }
                }
                
                
            }
          
        }
        $properties = collect($properties);
        if($request->has('min_price') && $request->has('max_price')){
            $properties = $properties->filter(function ($property) use ($request) {
                return $property['pl_price'] >= (integer)$request->min_price && $property['pl_price'] <= (integer)$request->max_price;
            })->values();
        }
      
        // if($request->sort_by === 'low_to_high') {
        //     $properties = $properties->sortBy('pl_price')->values();
        // }
        // else{
        //     $properties = $properties->sortByDesc('pl_price')->values();
        // }
        
        $sortBy = $request->sort_by ?? 'low_to_high'; // Default to "low_to_high"
        $properties = ($sortBy === 'low_to_high') 
        ? $properties->sortBy('pl_price')->values() 
        : $properties->sortByDesc('pl_price')->values();
        
        
        
        $defaultPerPage = 12;  
        $currentPage = $request->get('page', 1);
        
        // Increase the number of results dynamically (e.g., Page 2 = 24, Page 3 = 36, etc.)
        $itemsToShow = $defaultPerPage * $currentPage;
        
        $total = $properties->count();
        $paginatedProperties = $properties->slice(0, $itemsToShow)->values();
        
        $paginator = new LengthAwarePaginator(
            $paginatedProperties, 
            $total, 
            $itemsToShow, // This increases with each page
            $currentPage, 
            ['path' => $request->url()]
        );
        
        // Check if more pages exist
        $nextPage = $itemsToShow < $total ? $currentPage + 1 : null;


        
        $checkin_date = $request->input('checkin_date');
        $checkout_date = $request->input('checkout_date');
        $total_guests = $request->input('total_guests');
        $adultsCount = $request->input('adultsCount');
        $childrenCount = $request->input('childrenCount');
        $guestCount = $adultsCount + $childrenCount;
        
        $html = view('frontend.properties.ajax-filter-property', ['properties' => $paginator,
        
        'location_name' => "",
        'checkin_date' => $checkin_date,
        'checkout_date' => $checkout_date,
        'city_id' => $request->location,
        'total_guests' => $total_guests,
        'adultsCount' => $adultsCount,
        'childrenCount' => $childrenCount,
        'guestCount' => $guestCount,
        
        
        'nextPage' => $nextPage, 'locationName'=>''])->render();
    
        return response()->json([
            'html' => $html,
            'location_name' => '',
            'type'=>$request->type,
            'properties' => $paginator,
            'filterWithSorting'=>$request->filterWithSorting,
            'propertyCount'=>$paginator->count(),
            'total'=>$total,
            'nextPage' => $nextPage,
        ]);
    }

// end




    public function joinnetwork(){
        $intro = TblJoinOurNetworkIntro::where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('position', 'asc')
                    ->get();
        $faqs = TblJoinOurNetworkFaqs::where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('position', 'asc')
                    ->get();
        return view('frontend.join-our-network', compact('intro', 'faqs'));
    }



    public function aboutus(){
        $about_us = TblAboutUs::where('status', 1)
                    ->whereNull('deleted_at')
                    ->first(); // Retrieve the first row
        return view('frontend.about_us', compact('about_us'));
    }

    public function contactus(){
        return view('frontend.contact_us');
    }


    public function team(){
        $teams = TblTeam::where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('position', 'asc')
                    ->get();
        return view('frontend.team', compact('teams'));
    }


    public function faqs(){
        $faqs = TblFaq::where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('position', 'asc')
                    ->get();
        return view('frontend.faqs', compact('faqs'));
    }

    public function blogs(Request $request){
        // Get the search query from the URL parameter
        $searchQuery = $request->query('search');
        // Check if the search query exists
        if ($searchQuery) {
            // Check if the search query is a valid date
            if (strtotime($searchQuery)) {
                // If it's a valid date, search for blogs by date
                $blogs = TblBlog::where('status', 1)
                                ->whereDate('date', $searchQuery)
                                ->orderBy('position', 'asc')
                                ->get();
            }
            else{
                // If it's not a valid date, search for blogs by title
                $blogs = TblBlog::where('status', 1)
                                ->where('title', 'like', '%' . $searchQuery . '%')
                                ->orderBy('position', 'asc')
                                ->get();
            }
        }
        else{
            // If there's no search query, fetch all blogs
            $blogs = TblBlog::where('status', 1)
                            ->whereNull('deleted_at')
                            ->orderBy('position', 'asc')
                            ->get();
        }

        // Return the view with the filtered or all blogs
        return view('frontend.blog', compact('blogs'));
    }

    public function blogdetails($slug){
        // Retrieve the blog from the database based on the provided id
        $blog = TblBlog::where('slug', $slug)
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('position', 'asc')
                ->firstOrFail();
        $latestBlogs = TblBlog::where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('position', 'asc')
                ->latest()
                ->take(3)
                ->get();
        // Pass the retrieved blog to the view
        return view('frontend.blog_details', ['blog' => $blog , 'latestBlogs' => $latestBlogs]);
    }

    public function ourdiffrence(){
        $our_diffrence = TblOurDifference::where('status', 1)
                    ->whereNull('deleted_at')
                    ->orderBy('position', 'asc')
                    ->get();

        return view('frontend.our_diffrence', compact('our_diffrence'));
    }
    
    
    
  public function specialoffers(){
    $specialoffers = TblSpecialInvitation::where('status', 1)->orderBy('position', 'asc') ->get(); 
    return view('frontend.specialoffers.specialoffers', compact('specialoffers'));
}


    public function policy(){
        return view('frontend.policy');
    }

    public function booking_page(){
        return view('frontend.booking_page');
    }

    public function cancellation_refund(){
        return view('frontend.cancellation_refund');
    }
    
    
    
    // function ajaxFilterProperties(Request $request){
        
    //     $query = TblHome::query();
    //     if ($request->has('state') && $request->state != '') {
    //         $state = TblState::find($request->state);
    //         $locationName = $state ? $state->name : 'All';
    //         $query->where('state_id', $request->state);
    //     }
    //     if ($request->has('location') && $request->location !== 'all') {
    //         $location = TblLocation::find($request->location);
    //         $locationName = $location ? $location->location_name : 'All';
    //         $query->where('location_id', $request->location);
    //     }
    //     else{
    //         $locationName = 'All';
    //     }
    //     if ($request->has('propertyType') && $request->propertyType !== 'all') {
    //         $query->where('home_type_id', $request->propertyType);
    //     }
    //     if ($request->has('collection') && $request->collection == 'all') {
    //         //$query->where('collection_id', '!=',  0);
    //         $collections = TblCollection::get()->pluck('id');
    //         $Home_id = TblHomeCollection::whereIn('collection_id', $collections)->get()->pluck('home_id');
    //         $query->whereIn('id',$Home_id);
            
            
    //     }
    //     if ($request->has('noOfBedrooms')) {
    //         $query->where('no_of_bedrooms', '>=', $request->noOfBedrooms);
    //     }
    //     if ($request->has('tag') && $request->tag !== 'all') {
    //         $query->whereHas('amenities', function ($subQuery) use ($request) {
    //             $subQuery->where('amenities_id', $request->tag);
    //         });
    //     }
    //     $list = $query->with(['images', 'locationData','tags'])->where('status', 1)->whereNull('deleted_at')->get();

    //     $properties = [];
    //     foreach ($list as $property) {
    //         $price = 0;
    //         if($request->checkin_date !='' && $request->checkout_date !=''){
    //             $checkin_date = date('Y-m-d', strtotime($request->checkin_date));
    //             $checkout_date = date('Y-m-d', strtotime($request->checkout_date));
              
    //             $last_date =  date('Y-m-d', strtotime($checkout_date. '-1 days'));
                
    //             if($last_date == $checkin_date){
    //                 $date_difference_count = 1;
    //             }
    //             else{
    //                 $date_difference_count = MasterHelper::getDateDifference($checkin_date, $checkout_date);
    //             }
                
                
    //             $checkInDate = date('Y-m-d', strtotime($request->checkin_date));
    //             $checkOutDate = date('Y-m-d', strtotime($request->checkout_date));
                
               
                
                
                
    //             $count = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $checkout_date)->where('is_available', 'no')->count();
              
    //             $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [$checkin_date, $last_date])->sum('price');
    //             $price = round($price/$date_difference_count);
    //         }
    //         else{
    //             $propertyCheckInDate = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'yes')->where('availability_date', '>=', date('Y-m-d'))->orderBy('availability_date', 'asc')->first();
    //             if(isset($propertyCheckInDate->availability_date)){
    //                 $checkInDate =  $propertyCheckInDate->availability_date;
    //             }
    //             else{
    //                 $checkInDate =  date('Y-m-d'); 
    //             }
    //             $checkOutDate = date('Y-m-d', strtotime($checkInDate. '+1 days'));
    //             $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($checkInDate)),date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))])->sum('price');
    //         }
            
    //         $count = DB::table("ru_property_availabilities")
    //                 ->where("ru_property_id", $property->ru_property_id)
    //                 ->where("availability_date", ">=",$checkInDate)
    //                 ->where("availability_date", "<=",$checkOutDate)->where("is_available", "no")->count();
          
    //         if($price > 0 && $count ==0){
    //             if(setting()->website_markup){   
    //                 $price = $price +  ($price*setting()->website_markup)/100;
    //             }
    //             $pricea[] = $price;
    //             $property->pl_price = $price;
                
    //             if($price >= (integer)$request->minPrice){
    //                 $properties[] = $property;
    //             }
    //         }
    //     }
    //     $properties = collect($properties);
    //     if($request->has('minPrice') && $request->has('maxPrice')){
    //         $properties = $properties->filter(function ($property) use ($request) {
    //             return $property['pl_price'] >= (integer)$request->minPrice && $property['pl_price'] <= (integer)$request->maxPrice;
    //         })->values();
    //     }
      
    //     if($request->sort_by === 'low_to_high') {
    //         $properties = $properties->sortBy('pl_price')->values();
    //     }
    //     else{
    //         $properties = $properties->sortByDesc('pl_price')->values();
    //     }
    //     $defaultPerPage = 12;
    //     $currentPage = $request->get('page', 1);
    //     if($request->type == 'sorting'){
    //         $defaultPerPage = 12*$currentPage;
    //         if($currentPage >1){
    //             $currentPage = $currentPage -1;
    //         }
    //     }
        
    //     $total = $properties->count();
    //     $paginatedProperties = $properties->slice(($currentPage - 1) * $defaultPerPage, $defaultPerPage);
    //     $paginator = new LengthAwarePaginator($paginatedProperties, $total, $defaultPerPage, $currentPage,['path' => $request->url()]);
    //     $nextPage=  $currentPage + 1;
        
        
    //     $checkin_date = $request->input('checkin_date');
    //     $checkout_date = $request->input('checkout_date');
    //     $total_guests = $request->input('total_guests');
    //     $adultsCount = $request->input('adultsCount');
    //     $childrenCount = $request->input('childrenCount');
    //     $guestCount = $adultsCount + $childrenCount;
        
    //     $html = view('frontend.properties.ajax-filter-property', ['properties' => $paginator,
        
    //     'location_name' => $locationName,
    //     'checkin_date' => $checkin_date,
    //     'checkout_date' => $checkout_date,
    //     'city_id' => $request->location,
    //     'total_guests' => $total_guests,
    //     'adultsCount' => $adultsCount,
    //     'childrenCount' => $childrenCount,
    //     'guestCount' => $guestCount,
        
        
    //     'nextPage'=>$nextPage, 'locationName'=>$locationName])->render();
    
    //     return response()->json([
    //         'html' => $html,
    //         'location_name' => $locationName,
    //         'type'=>$request->type,
    //         'filterWithSorting'=>$request->filterWithSorting,
    //         'propertyCount'=>$paginator->count()
    //     ]);
    // }
    
    
    public function landing(){
        session()->forget([
            'location_name',
            'checkin_date',
            'checkout_date',
            'city_id',
            'total_guests',
            'adultsCount',
            'childrenCount',
        ]);
       
        $properties = TblHome::with(['images','locationData','tags'])
        ->where('show_on_home', 1)
        ->where('status', 1)
        ->whereNull('deleted_at')
        ->get();
        
        $propertiesApartment = TblHome::with(['images','locationData','tags'])
        ->where('show_on_apartment', 1)
        ->where('status', 1)
        ->whereNull('deleted_at')
        ->get();
        
        $locations = TblLocation::with('state') 
         ->where('status', 1) 
          ->where('show_on_location_page', 1) 
          ->get();
          $blogs = TblBlog::where('status', 1)->where('show_on_blog_page',1)->get();
         $collections = TblCollection::query()
         ->where('status', 1)
          ->where('show_on_collection_page', 1)
           ->get();

         $specialoffers = TblSpecialInvitation::where('status', 1)
    ->with('discountCoupon')
    ->orderBy('position', 'asc') 
    ->take(3) // Limit the results
    ->get();

     $reviews = TblHomeReview::limit(10)->get();

       $addventures = HomeAddventure::all();

        $footerHomeData = HomeFooterBanner::first();
        $footerHomeData->list_content = json_decode($footerHomeData->list_content, true);
        $home_banner = TblHomeBanner::with('homeBannerImage','homeBannerVideo')->where('status', 1)->whereNull('deleted_at')->first();
        
        return view('frontend.landing', compact('properties','home_banner', 'propertiesApartment', 'footerHomeData','locations','reviews','addventures','blogs','specialoffers','collections'));
    }
    


}
