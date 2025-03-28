<?php

namespace App\Http\Controllers\PmsApi;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\TblHome;
use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use App\helper\MasterHelper;
use Illuminate\Support\Collection;
use App\Models\BookingGuestId;
use App\Models\BookingEnquiry;
use App\Models\TblGst;
use App\Models\User;
use App\Exports\BookingExport;
use App\Mail\BookingConfirmationEmail;
use App\Services\RazorpayService;
use App\Exports\SaleReportExport;
use App\Exports\PoliceVerificationReport;
use DB;
use Mail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PropertyController extends Controller{


    public function propertyList(Request $request){
        try {
            $no_of_guests = $request->no_adults + $request->no_children;
            $last_date =  date('Y-m-d', strtotime($request->checkout_date. '-1 days'));
            $checkin_date =  $request->checkin_date;
            if($last_date == $request->checkin_date){
                $date_difference_count = 1;
            }
            else{
                $date_difference_count = MasterHelper::getDateDifference($request->checkin_date, $request->checkout_date);
            }
            $location_id = $request->location_id;
            $query = TblHome::query();
            $query->when($location_id != '', function ($q) use ($location_id) {
                return $q->where('location_id', $location_id);
            });
            $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
                return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
            });
            $list = $query->with('additionalCharge')->whereNotNull('ru_property_id')->get();
            $filtered_property_list = array();
            if(!empty($list)){
                foreach($list as $detail){
                    //$checkIsAvailable = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', $checkin_date)->first(['is_available', 'ru_property_id']);
                    $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $request->checkin_date)->where('availability_date', '<=', $request->checkout_date)->where('is_available', 'no')->count();
                    $price = 0;
                    $is_available = 'yes';
                    if($count > 0){
                        $is_available = 'no';
                    }
                    if($request->existing_property_id){
                        $propertyBooking = PropertyBooking::where('id', $request->existing_property_id)->first(['property_id', 'tax_inclusive']);
                        if($propertyBooking->property_id == $detail->id){
                            $is_available = 'yes';
                        }
                    }
                    if($is_available == 'yes'){
                        $price = $initial_price = 0;
                        do {
                            $xmlReqForPropertyPrice = "<Pull_ListPropertyPrices_RQ>
                                <Authentication>
                                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                    <Password>".config('ru.RU_PASSWORD')."</Password>
                                </Authentication>
                                <PropertyID>".$detail->ru_property_id."</PropertyID>
                                <DateFrom>".$checkin_date."</DateFrom>
                                <DateTo>".$checkin_date."</DateTo>
                            </Pull_ListPropertyPrices_RQ>";
                            $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);
                            if($ruPropertyPriceResponse){
                                if(isset($ruPropertyPriceResponse['data']['Prices'])){
                                    if(isset($ruPropertyPriceResponse['data']['Prices']['Season'])){
                                        if(isset($ruPropertyPriceResponse['data']['Prices']['Season']['Price'])){
                                            $price = $price +  $ruPropertyPriceResponse['data']['Prices']['Season']['Price'];
                                        }
                                    }
                                }
                            }
                            $checkin_date = date('Y-m-d', strtotime($checkin_date . '+1 day'));
                        }while ($checkin_date <=$last_date);
                    }
                    if($price >0){
                        $gst_amount = 0;
                        $gstPrecentage = 0;
                        $getAppliedGst  = getAppliedGst($price);
                        if($getAppliedGst){
                            $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                            $gst_amount = $precentageAmount;
                            $gstPrecentage = $getAppliedGst->gst_percentage;
                        }
                        if($request->tax_inclusive == 1){
                            $price =  $price + $gst_amount;
                        }
                        $detail->price = $price;
                        $detail->per_night_price = $price/$date_difference_count;
                        $detail->initial_price = $price;
                        $detail->gst_amount = $gst_amount;
                        $detail->gst_percentage = $gstPrecentage;
                        $extra_no_of_guest = 0;
                        $extra_guest_charge = 0;

                        if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){
                            if($detail->maximum_number_of_guests == $no_of_guests){
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                            }
                            else{
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                            }
                            $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;
                            if($request->tax_inclusive == 1){
                                $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                                if($getAppliedGst){
                                    $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;
                                    $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                                }
                            }
                        }
                        $detail->extra_no_of_guest = $extra_no_of_guest;
                        $detail->final_extra_guest_charge = $extra_guest_charge;
                        array_push($filtered_property_list, $detail);
                    }
                }
            }
            $gst_slab = TblGst::get();
            if($request->existing_property_id){
                $tax_inclusive = $propertyBooking->tax_inclusive;
            }
            else{
                $tax_inclusive = $request->tax_inclusive;
            }
            return response()->json([
                'status' => true,
                'data' => array('property_list'=>$filtered_property_list, 'no_of_nights'=>$date_difference_count, 'is_gst_allowed'=>setting()->is_allow_gst, 'states'=>MasterHelper::getStateList(), 'gst_slab'=>$gst_slab, 'tax_inclusive'=>$tax_inclusive),
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

    public function propertyListById(Request $request){
        try {
            $location_id = $request->location_id;
            $propertyId = '';
            if($request->existing_property_id){
                $propertyBooking = PropertyBooking::where('id', $request->existing_property_id)->first();
                $propertyId = $propertyBooking->property_id;
            }
            $query = TblHome::query();

            $query->when($location_id != '', function ($q) use ($location_id) {
                return $q->where('location_id', $location_id);
            });
            $query->when($request->existing_property_id, function ($q) use ($propertyId) {
                return $q->where('id', $propertyId);
            });
            $list = $query->with('additionalCharge')->whereNotNull('ru_property_id')->get();
            $filtered_property_list = array();
            if(!empty($list)){
                foreach($list as $detail){
                    $price = 0.00;
                    $per_night_price = 0.00;
                    $detail->price = $price;
                    $detail->per_night_price = $per_night_price;
                    array_push($filtered_property_list, $detail);
                }
            }
            return response()->json([
                'status' => true,
                'data' => array('property_list'=>$filtered_property_list, 'no_of_nights'=>0),
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


    public function propertyAvailabilityDetail(Request $request){
        try {
            $no_of_guests = $request->no_adults + $request->no_children;
            $id = $request->propertyId;
            $last_date =  date('Y-m-d', strtotime($request->checkout_date. '-1 days'));
            $checkin_date =  $request->checkin_date;
            if($last_date == $request->checkin_date){
                $date_difference_count = 1;
            }
            else{
                $date_difference_count = MasterHelper::getDateDifference($request->checkin_date, $request->checkout_date);
            }
            $location_id = $request->location_id;
            $propertyId = $request->propertyId;
            $query = TblHome::query();
            $query->when($location_id != '', function ($q) use ($location_id) {
                return $q->where('location_id', $location_id);
            });
            $query->when($id, function ($q) use ($id) {
                return $q->where('id', $id);
            });
            $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
                return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
            });
            $detail = $query->with('additionalCharge')->where('maximum_number_of_guests', '>=', $no_of_guests)->whereNotNull('ru_property_id')->first();

            $filtered_property_list = array();
            if(!empty($detail)){
                $checkIsAvailable = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', $checkin_date)->first('is_available');
                $price = 0;
                $is_available = 'yes';
                if($request->existing_property_id){
                    $propertyBooking = PropertyBooking::where('id', $request->existing_property_id)->first();
                    if($propertyBooking->property_id == $detail->id){
                        $is_available = 'yes';
                    }
                    else{
                        $is_available = $checkIsAvailable->is_available;
                    }
                }
                else{
                    $is_available = $checkIsAvailable->is_available;
                }
                if($is_available =='yes'){
                    $price = $initial_price = 0;
                    do {
                        $xmlReqForPropertyPrice = "<Pull_ListPropertyPrices_RQ>
                            <Authentication>
                                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                <Password>".config('ru.RU_PASSWORD')."</Password>
                            </Authentication>
                            <PropertyID>".$detail->ru_property_id."</PropertyID>
                            <DateFrom>".$checkin_date."</DateFrom>
                            <DateTo>".$checkin_date."</DateTo>
                        </Pull_ListPropertyPrices_RQ>";
                        $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);
                        if($ruPropertyPriceResponse){
                            if(isset($ruPropertyPriceResponse['data']['Prices'])){
                                if(isset($ruPropertyPriceResponse['data']['Prices']['Season'])){
                                    if(isset($ruPropertyPriceResponse['data']['Prices']['Season']['Price'])){
                                        $price = $price +  $ruPropertyPriceResponse['data']['Prices']['Season']['Price'];
                                    }
                                }
                            }
                        }
                        $checkin_date = date('Y-m-d', strtotime($checkin_date . '+1 day'));
                    }while ($checkin_date <=$last_date);
                }
                if($price >0){
                    $gst_amount = 0;
                    $gstPrecentage = 0;
                    $getAppliedGst  = getAppliedGst($price);
                    if($getAppliedGst){
                        $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                        $gst_amount = $precentageAmount;

                        $gstPrecentage = $getAppliedGst->gst_percentage;
                    }
                    if($request->tax_inclusive == 1){
                        $price =  $price + $gst_amount;
                    }
                    $detail->price = $price;
                    $detail->per_night_price = $price/$date_difference_count;
                    $detail->initial_price = $price;
                    $detail->gst_amount = $gst_amount;
                    $detail->gst_percentage = $gstPrecentage;

                    $extra_no_of_guest = 0;
                    $extra_guest_charge = 0;

                    if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){
                        if($detail->maximum_number_of_guests == $no_of_guests){
                            $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                        }
                        else{
                            $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                        }
                        $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;
                        if($request->tax_inclusive == 1){
                            $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                            if($getAppliedGst){
                                $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;
                                $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                            }
                        }
                    }
                    $detail->extra_no_of_guest = $extra_no_of_guest;
                    $detail->final_extra_guest_charge = $extra_guest_charge;
                }
            }
            $gst_slab = TblGst::get();
            if($request->existing_property_id){
                if($request->existing_property_id == $propertyId){
                    $discount_amount = $propertyBooking->discount_amount?$propertyBooking->discount_amount:'';
                }
                else{
                    $discount_amount = '';

                }
                $tax_inclusive = $propertyBooking->tax_inclusive;

            }
            else{
                $tax_inclusive = $request->tax_inclusive;
                $discount_amount = '';
            }
            return response()->json([
                'status' => true,
                'data' => array('propertyDetail'=>$detail, 'no_of_nights'=>$date_difference_count, 'is_gst_allowed'=>setting()->is_allow_gst, 'gst_slab'=>$gst_slab , 'tax_inclusive'=>$tax_inclusive, 'discount_amount'=>$discount_amount),
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


    public function savePropertyBooking(Request $request){
       
        $validator = Validator::make($request->all(), [
            'checkInDate' => 'required',
            'checkOutDate' => 'required',
            'email_address' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'propertyId' => 'required',
            'locationId' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->first()
            ], 500);
        }
        try{
            $lastEntery = PropertyBooking::orderBy('id', 'desc')->first();
            if($lastEntery){
                $count = $lastEntery->id;
            }
            else{
                $count = 1;
            }
            $propertyBooking = PropertyBooking::firstOrNew(['id'=>$request->id]);
            $propertyBooking->location_id = $request->locationId;
            $propertyBooking->property_id = $request->propertyId;
            $propertyBooking->total_amount = round($request->netAmount);
            $propertyBooking->discount_amount = $request->discount_amount?round($request->discount_amount):NULL;
            $propertyBooking->payable_amount = round($request->netPayableAmount);
            $propertyBooking->additional_charges = $request->additional_charges?json_encode($request->additional_charges):NULL;
            $propertyBooking->tax_amount = $request->taxAmount;
            if(!$request->id){
                $propertyBooking->booking_id = rand(10000000, 99999999);
            }
            
            $propertyBooking->booking_status = 'pending';
            $propertyBooking->booking_created_by = 'admin';
            $propertyBooking->type = $request->type;
            $propertyBooking->no_of_children = $request->no_children;
            $propertyBooking->no_of_adult = $request->no_adult?$request->no_adult:1;
            $propertyBooking->customer_detail = json_encode(array('first_name'=>$request->first_name, 'last_name'=>$request->last_name, 'email'=>$request->email_address , 'mobile_number'=>$request->mobile_number, 'note'=>$request->note));
            $propertyBooking->checkin_date = $request->checkInDate;
            $propertyBooking->checkout_date = $request->checkOutDate;
            $propertyBooking->per_night_price = $request->per_night_price;
            $propertyBooking->no_of_night = $request->noOfNights;
            $propertyBooking->tax_inclusive = $request->tax_inclusive;
            $propertyBooking->channel = 'PMS';
            if(isset($request->extraGuestCharge)){
                $propertyBooking->extra_guest_charge = $request->extraGuestCharge;
            }
            if(isset($request->totalTaxableAmount)){
                $propertyBooking->taxable_amount = $request->totalTaxableAmount;
            }
            if(isset($request->tax)){
                $propertyBooking->tax = $request->tax;
            }
            if($request->has('booking_created_by')){
                $propertyBooking->created_by = $request->booking_created_by;
            }
            $propertyBooking->save();
            $home = TblHome::where('id', $request->propertyId)->first();

            $message = 'Booking Saved successfully';
            if($request->id !=''){
                $message = 'Booking Updated Successfully';
            }
            blockPropertyAvailabilityInRu($home->ru_property_id, $request->checkInDate , $request->checkOutDate);
            $emailData =  array('id'=>$request->id, 'bookingDetail'=>$propertyBooking);
            Mail::to($request->email_address)->send(new BookingConfirmationEmail($emailData));
            //Mail::to(env('TISYA_SUPPORT_EMAIL'))->send(new BookingConfirmationEmail($emailData));
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => $message
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function getPropertyBooking($id){
        try{
            $detail = PropertyBooking::query()
            ->when(isset($id), function ($query) use ($id) {
                return $query->where('property_bookings.id', $id);
            })
            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('paymentRequests')->orderBy('id', 'desc')->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*', 'property_bookings.tax_inclusive']);
            $tax_inclusive = PropertyBooking::where('id', $id)->first('tax_inclusive');
            return response()->json([
                'status' => true,
                'data' => $detail,
                'tax_inclusive' =>$tax_inclusive->tax_inclusive,
                'message' => 'Booking detail listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
    
    // public function propertyBookingList(Request $request, $role=NULL, $userId=NULL){
    //     try{
    //         $list = PropertyBooking::query()
    //             ->when(isset($request->property_id) && $request->property_id !='', function ($query) use ($request) {
    //                 return $query->where('property_id', $request->property_id);
    //             })
    //             ->when(isset($role) && $role == 'Owner', function ($query) use ($userId) {
    //                 return $query->where('tbl_homes.user_id', $userId);
    //             })
    //             ->when(isset($request->booking_id) && $request->booking_id !='', function ($query) use ($request) {
    //                 return $query->where('booking_id', $request->booking_id);
    //             })
    //             ->when(isset($request->channel) && $request->channel !='', function ($query) use ($request) {
    //                 return $query->where('channel', $request->channel);
    //             });

    //         if (isset($request->type) && $request->type !='') {
    //             if ($request->type == 'checkin') {
    //                 $list = $list->when(isset($request->checkin_date) && isset($request->checkout_date), function ($query) use ($request) {
    //                         return $query->where('checkin_date', '>=', $request->checkin_date)
    //                                     ->where('checkin_date', '<=', $request->checkout_date);
    //                     })
    //                     ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
    //                     ->with('paymentRequests')
    //                     ->orderBy('checkin_date', 'asc')
    //                     ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
    //             } else {
    //                 $list = $list->when(isset($request->checkin_date) && isset($request->checkout_date), function ($query) use ($request) {
    //                         return $query->where('checkout_date', '>=', $request->checkin_date)
    //                                     ->where('checkout_date', '<=', $request->checkout_date);
    //                     })
    //                     ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
    //                     ->with('paymentRequests')
    //                     ->orderBy('checkout_date', 'asc')
    //                     ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
    //             }
    //         } else {
    //             $list = $list->when(isset($request->checkin_date) && isset($request->checkout_date), function ($query) use ($request) {
    //                     return $query->where('checkin_date', '>=', $request->checkin_date)
    //                                 ->where('checkin_date', '<=', $request->checkout_date);
    //                 })
    //                 ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
    //                 ->with('paymentRequests')
    //                 ->orderBy('id', 'desc')
    //                 ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
    //         }
    //         $finalData = array();
    //         foreach($list as $key=>$value){
    //             $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $value->id)->where('booking_request_status', 'Payment Received')->sum('amount');
    //             $payment_status = 'pending';
    //             $booking_status = 'not_confirmed';
    //             if($paidAmount == $value->payable_amount){
    //                 $payment_status = 'paid';
    //                 $booking_status = 'confirmed';
    //             }

    //             $search_payment_status = '';
    //             $search_booking_status = '';
    //             if(isset($request->booking_status)){
    //                 $search_booking_status = $request->booking_status;
    //             }
    //             if(isset($request->payment_status)){
    //                 $search_payment_status = $request->payment_status;
    //             }

    //             if($value->booking_created_by == 'ru'){
    //                 $payment_status = 'paid';
    //                 $booking_status = 'confirmed';
    //             }

    //             if($search_payment_status !='' && $search_booking_status ==''){
    //                 if($search_payment_status==$payment_status){
    //                     array_push($finalData, $value);
    //                 }
    //             }
    //             else if($search_payment_status =='' && $search_booking_status !=''){
    //                 if($search_booking_status==$booking_status){
    //                     array_push($finalData, $value);
    //                 }
    //             }
    //             else if($search_payment_status !='' && $search_booking_status !=''){
    //                 if($search_booking_status==$booking_status && $search_payment_status==$payment_status){
    //                     array_push($finalData, $value);
    //                 }
    //             }
    //             else{
    //                 array_push($finalData, $value);
    //             }
    //         }

    //         $disableDatesArray = array();
    //         if(isset($role) && $role == 'Owner'){
    //             if($finalData){
    //                 foreach($finalData as $booking){
    //                     $startDate = Carbon::create(date('Y-m-d', strtotime($booking->checkin_date)));
    //                     $endDate = Carbon::create(date('Y-m-d', strtotime($booking->checkout_date)));
    //                     for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
    //                         array_push($disableDatesArray, $date->toDateString());
    //                     }
    //                 }
    //             }
    //         }

    //         $proeprtyQuery = TblHome::query()
    //         ->when(isset($role) && $role =='Owner', function ($proeprtyQuery) use ($userId) {
    //             return $proeprtyQuery->where('user_id', $userId);
    //         })->with('additionalCharge')->whereNotNull('ru_property_id')->get();
 

    //         // dd($request->all());
    //         return response()->json([
    //             'status' => true,
    //             'data' => $finalData,
    //             'proeprtyList'=>$proeprtyQuery,
    //             'disableDatesArray'=>$disableDatesArray,
    //             'message' => 'Booking Listed Successfully.'
    //         ], 200);
    //     }
    //     catch(Exception $e){
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }
public function propertyBookingList(Request $request, $role=NULL, $userId=NULL){
        try{
            $list = PropertyBooking::query()
            ->when(isset($request->property_id), function ($query) use ($request) {
                return $query->where('property_id', $request->property_id);
            })
            ->when(isset($role) && $role =='Owner', function ($query) use ($userId) {
                return $query->where('tbl_homes.user_id', $userId);
            })
            ->when(isset($request->booking_id), function ($query) use ($request) {
                return $query->where('booking_id', $request->booking_id);
            })
            ->when(isset($request->channel), function ($query) use ($request) {
                return $query->where('channel', $request->channel);
            })
            ->when(isset($request->payment_status), function ($query) use ($request) {
                return $query->where('payment_status', $request->payment_status);
            })
            ->when(isset($request->booking_status), function ($query) use ($request) {
                return $query->where('property_booking_status', $request->booking_status);
            })
            ->when(isset($request->checkin_date) && isset($request->checkout_date), function ($query) use ($request) {
                return $query->where('checkin_date', '>=', $request->checkin_date)->where('checkout_date', '<=', $request->checkout_date);
            })
            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('paymentRequests')->where('payable_amount', '>', 0)->orderBy('id', 'desc')->get(['tbl_homes.home_name', 'tbl_homes.internal_name', 'tbl_homes.ru_property_id',  'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
            $proeprtyQuery = TblHome::query()
            ->when(isset($role) && $role =='Owner', function ($proeprtyQuery) use ($userId) {
                return $proeprtyQuery->where('user_id', $userId);
            })->with('additionalCharge')->whereNotNull('ru_property_id')->get();
            return response()->json([
                'status' => true,
                'data' => $list,
                'proeprtyList'=>$proeprtyQuery,
                'message' => 'Booking Listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function propertyBookingListTest(Request $request, $role=NULL, $userId=NULL){
        
 
        try{
            $query = PropertyBooking::query()
            ->when(isset($request->property_id) && $request->property_id !='', function ($query) use ($request) {
                return $query->where('property_id', $request->property_id);
            })
            ->when(isset($role) && $role == 'Owner', function ($query) use ($userId) {
                return $query->where('tbl_homes.user_id', $userId);
            })
            ->when(isset($request->booking_id) && $request->booking_id !='', function ($query) use ($request) {
                return $query->where('booking_id', $request->booking_id);
            })
            ->when($request->has('payment_status') && $request->payment_status !='' , function ($query) use ($request) {
                return $query->where('booking_status', $request->payment_status);
            })
            ->when($request->has('booking_status') && $request->booking_status !='', function ($query) use ($request) {
                return $query->where('property_booking_status', $request->booking_status);
            })
            ->when($request->has('created_by') && $request->created_by !='', function ($query) use ($request) {
                return $query->where('created_by', $request->created_by);
            })
            ->when($request->has('channel') && $request->channel !='', function ($query) use ($request) {
                return $query->where('channel', $request->channel);
            });
           
            if (isset($request->type)) {
                if ($request->type == 'checkin') {
                    $query = $query->when(isset($request->checkin_date) && isset($request->checkout_date), function ($query) use ($request) {
                            return $query->where('checkin_date', '>=', $request->checkin_date)
                                        ->where('checkin_date', '<=', $request->checkout_date);
                        })
                        ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                        ->with('paymentRequests')
                        ->orderBy('checkin_date', 'asc')
                        ->select(['tbl_homes.home_name', 'tbl_homes.internal_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
                }
                else {
                    $query = $query->when(isset($request->checkin_date) && isset($request->checkout_date), function ($query) use ($request) {
                            return $query->where('checkout_date', '>=', $request->checkin_date)
                                        ->where('checkout_date', '<=', $request->checkout_date);
                        })
                        ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                        ->with('paymentRequests')
                        ->orderBy('checkout_date', 'asc')
                        ->select(['tbl_homes.home_name', 'tbl_homes.internal_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
                }
            }
            else {
                $query = $query->when(isset($request->checkin_date) && isset($request->checkout_date), function ($query) use ($request) {
                        return $query->where('checkin_date', '>=', $request->checkin_date)
                                    ->where('checkin_date', '<=', $request->checkout_date);
                    })
                    ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
                    ->with('paymentRequests')
                    ->orderBy('id', 'desc')
                    ->select(['tbl_homes.home_name', 'tbl_homes.internal_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
            }

            $perPage = $request->has('take') ? $request->get('take') : 20;
            $list = $query->with('user')->paginate($perPage);

            // Transform data if necessary
            $finalData = $list; // Transform items if needed

            $proeprtyQuery = TblHome::query()
            ->when(isset($role) && $role =='Owner', function ($proeprtyQuery) use ($userId) {
                return $proeprtyQuery->where('user_id', $userId);
            })->with('additionalCharge')->whereNotNull('ru_property_id')->get();

            $disableDatesArray = array();
            if(isset($role) && $role == 'Owner'){
                if($finalData){
                    foreach($finalData as $booking){
                        $startDate = Carbon::create(date('Y-m-d', strtotime($booking->checkin_date)));
                        $endDate = Carbon::create(date('Y-m-d', strtotime($booking->checkout_date)));
                        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                            array_push($disableDatesArray, $date->toDateString());
                        }
                    }
                }
            }


            $users = User::whereIn('role', ['Admin', 'Reservations'])->where('status', 1)->get();

            return response()->json([
                'status' => true,
                'data' => $finalData,
                'proeprtyList' => $proeprtyQuery,
                'disableDatesArray' => $disableDatesArray,
                'users' =>$users,
                'message' => 'Booking Listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // public function getPropertyBookingDetail($id){
    //     try{
    //         $bookingDetail = PropertyBooking::leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('bookingGuests')->where('property_bookings.id', $id)->first();

    //         $bookingDetail->bookingGuestsIds = BookingGuestId::where('property_booking_id', $id)->get();
    //         $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $id)->sum('amount');
    //         $availableAmount = 0.00;
    //         if($paidAmount < $bookingDetail->payable_amount){
    //             $availableAmount = $bookingDetail->payable_amount - $paidAmount;
    //         }
    //         return response()->json([
    //             'status' => true,
    //             'data' => $bookingDetail,
    //             'availableAmount' =>$availableAmount,
    //             'id_path' =>'/storage/guest_idproof/',
    //             'tax_inclusive'=>$bookingDetail->tax_inclusive,
    //             'message' => 'Booking Listed Successfully.'
    //         ], 200);
    //     }
    //     catch(Exception $e){
    //         return response()->json([
    //             'status' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }



public function getPropertyBookingDetail($id){
        try{
           // dd($id);
            $bookingDetail = PropertyBooking::leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('bookingGuests')->where('property_bookings.id', $id)->first();
            $bookingDetail->bookingGuestsIds = BookingGuestId::where('property_booking_id', $id)
            ->get()
            ->map(function ($guest) {
                $idProofImg = $guest->id_proof_img;

                if ($idProofImg) {
                    $decoded = json_decode($idProofImg, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $idProofImgs = array_map(function ($image) {
                            $image['filepath'] = '/storage/guest_idproof/' . $image['filename'];
                            return $image;
                        }, $decoded);
                    } else {
                        $idProofImgs = [
                            [
                                'filename' => $idProofImg,
                                'filepath' => '/storage/guest_idproof/' . $idProofImg,
                                'extension' => pathinfo($idProofImg, PATHINFO_EXTENSION),
                            ]
                        ];
                    }
                } else {
                    $idProofImgs = [];
                }

                return [
                    'id' => $guest->id,
                    'property_booking_id' => $guest->property_booking_id,
                    'name' => $guest->name,
                    'email' => $guest->email,
                    'mobile_no' => $guest->mobile_no,
                    'checkin_date' => $guest->checkin_date,
                    'checkout_date' => $guest->checkout_date,
                    'status' => $guest->status,
                    'created_at' => $guest->created_at,
                    'updated_at' => $guest->updated_at,
                    'dob' => $guest->dob,
                    'anniversary' => $guest->anniversary,
                    'id_proof_imgs' => $idProofImgs,
                ];
            });
            $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $id)->sum('amount');
            $availableAmount = 0.00;
            if($paidAmount < $bookingDetail->payable_amount){
                $availableAmount = $bookingDetail->payable_amount - $paidAmount;
            }
            return response()->json([
                'status' => true,
                'data' => $bookingDetail,
                'availableAmount' =>$availableAmount,
                'id_path' =>'/storage/guest_idproof/',
                'message' => 'Booking Listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saveBookingPaymentRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'amount' => 'required',
            'property_booking_id' => 'required',
            'payment_mode' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->first()
            ], 500);
        }
        try{
            $propertyBookingDetail = PropertyBooking::where('id', $request->property_booking_id)->first();
            $lastEntery = PropertyBookingPaymentRequest::orderBy('id', 'desc')->first();
            if($lastEntery){
                $count = $lastEntery->id;
            }
            else{
                $count = 1;
            }
            if($request->booking_request_id ==''){
                $detail = new PropertyBookingPaymentRequest();
                $detail->booking_request_id = rand(10000000, 99999999);
                $detail->property_booking_id = $request->property_booking_id;
                $detail->name = $request->name;
                $detail->email = $request->email;
                $detail->amount = $request->amount;
                $detail->payment_mode = $request->payment_mode;
                $detail->booking_request_status = 'Pending';
                $detail->save();
                $amount = $detail->amount;
                if($request->payment_mode == 'Cash' || $request->payment_mode == 'NEFT'){
                    $bookingPaidAmount = $propertyBookingDetail->paid_amount + $amount;
                    PropertyBooking::where('id', $request->property_booking_id)->update(['paid_amount'=>$bookingPaidAmount]);
                }
                else{
                    $paymentRequestDetail = PropertyBookingPaymentRequest::where('id', $detail->id)->first();
                    $payload = array('booking_id'=>$paymentRequestDetail->property_booking_id, 'booking_payment_request_id'=>$paymentRequestDetail->id, 'amount'=>$paymentRequestDetail->amount, 'name'=>$paymentRequestDetail->name, 'email'=>$paymentRequestDetail->email, 'contact_no'=>'911111111111');
                    RazorpayService::createPaymentLink($payload);
                }
            }
            else{
                $amount = $request->amount;
                $detail = $paymentRequestDetail = PropertyBookingPaymentRequest::where('booking_request_id', $request->booking_request_id)->first();
                $paidAmount  = $propertyBookingDetail->paid_amount + $amount - $paymentRequestDetail->amount;
                PropertyBooking::where('id', $request->property_booking_id)->update(['paid_amount'=>$paidAmount]);
                PropertyBookingPaymentRequest::where('booking_request_id', $request->booking_request_id)->update(['amount'=>$amount]);
            }
            $propertyBookingDetail = PropertyBooking::where('id', $request->property_booking_id)->first();
            return response()->json([
                'status' => true,
                'data' => $detail,
                'propertyBookingDetail'=>$propertyBookingDetail,
                'message' => 'Payment Requested Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function updatePaymentRequest(Request $request){
        $validator = Validator::make($request->all(), [
            'note' => 'required',
            'id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->first()
            ], 500);
        }
        try{
            $detail = PropertyBookingPaymentRequest::findOrFail($request->id);
            $detail->note = $request->note;
            $detail->booking_request_status = $request->booking_request_status;
            $detail->save();

            $bookingDetail = PropertyBooking::where('property_bookings.id', $detail->property_booking_id)->first();
            $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $detail->property_booking_id)->where('booking_request_status', 'Payment Received')->sum('amount');

            $availableAmount = 0.00;
            $status = 'Paid';

            if($request->booking_request_status =='Payment Received'){
                PropertyBooking::where('property_bookings.id', $detail->property_booking_id)->update(['property_booking_status'=>'Confirmed']);
            }
            if($paidAmount < $bookingDetail->payable_amount){
                $status = 'Pending';
            }
            else{
                PropertyBooking::where('property_bookings.id', $detail->property_booking_id)->update(['booking_status'=>'paid', 'paid_amount'=>$bookingDetail->payable_amount]);
            }
            return response()->json([
                'status' => true,
                'data' => '',
                'status'=>$status,
                'message' => 'Data Updated Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function updatePaymentRequestStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'booking_request_status' => 'required',
            'id' => 'required'
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->first()
            ], 500);
        }
        try{
            $detail =  PropertyBookingPaymentRequest::findOrFail($request->id);
            $detail->booking_request_status = $request->booking_request_status;
            $detail->save();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Status Changed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getPropertyBookingUnavailableDates(Request $request){
        try {
            $id = $request->id;
            $detail = TblHome::where('id', $id)->whereNotNull('ru_property_id')->first();
            $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();
            
            $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $detail->id)->where('checkout_date', '>', date('Y-m-d'))->where('property_booking_status', 'Confirmed')->pluck('checkout_date')->toArray();

            $propertyUnavailableDates = array_unique($propertyUnavailableDates);
            $previouslyBookedCheckoutDates = array_unique($previouslyBookedCheckoutDates);
            $updatedUnavailableDates = array_values(array_diff($propertyUnavailableDates, $previouslyBookedCheckoutDates));
            
            
            
            
            if($request->bookingId){
                $detail = PropertyBooking::where('id', $request->bookingId)->first();
                $startDate =  date('Y-m-d', strtotime($detail->checkin_date));
                $endDate =  date('Y-m-d', strtotime($detail->checkout_date));
                $startDate = Carbon::create($startDate); // YYYY, MM, DD
                $endDate = Carbon::create($endDate); // YYYY, MM, DD
                for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                    $cDate = $date->toDateString();
                    $propertyUnavailableDates = array_diff($propertyUnavailableDates, [$cDate]);
                    $propertyUnavailableDates = array_values($propertyUnavailableDates);
                }
            }
            
            return response()->json([
                'status' => true,
                'propertyUnavailableDates' => $updatedUnavailableDates,
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


    public function getPaymentRequestDetail($id){
        try{
            $propertyBookingPaymentRequest = PropertyBookingPaymentRequest::where('id', $id)->first();
            $bookingDetail = PropertyBooking::leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->where('property_bookings.id', $propertyBookingPaymentRequest->property_booking_id)->first(['property_bookings.*', 'tbl_homes.home_name']);

            $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $propertyBookingPaymentRequest->property_booking_id)->where('id', '!=', $id)->sum('amount');
            $availableAmount = 0.00;
            if($paidAmount < $bookingDetail->payable_amount){
                $availableAmount = $bookingDetail->payable_amount - $paidAmount;
            }

            return response()->json([
                'status'=>true,
                'data'=>$bookingDetail,
                'availableAmount'=>$availableAmount,
                'propertyBookingPaymentRequest'=>$propertyBookingPaymentRequest,
                'message' => 'Booking Listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteBooking($id){
        try{
            $bookingDetail = PropertyBooking::where(['id' => $id])->first();
            $home  = TblHome::where('id', $bookingDetail->property_id)->first();
            unBlockPropertyAvailabilityInRu($home->ru_property_id, $bookingDetail->checkin_date, $bookingDetail->checkout_date);

            $delete = PropertyBooking::where(['id' => $id])->delete();

            $emailData =  array('id'=>'', 'bookingDetail'=>$bookingDetail);
            // Mail::to($bookingDetail->customer_detail['email'])->send(new BookingCancellationEmail($emailData));
            return response()->json([
                'status' => true,
                'message'=> "Successfully Deleted"
            ],200);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message'=> "Internal Error",
                'error' => $e->getMessage()
            ], 500);
        }
    }


    // public function savePropertyBookingIds(Request $request){
    //     try{
    //         foreach($request->guest_list as $key=>$guest){
    //             $guestDetail  = BookingGuestId::where(['id'=>$guest['id']])->first();

    //             $detail = array();
    //             $detail['property_booking_id'] = $request->property_booking_id;
    //             $detail['name'] = $guest['name'];
    //             $detail['email'] = $guest['email'];
    //             $detail['mobile_no'] = $guest['mobile_no'];
    //             $detail['checkin_date'] = $request->checkin_date;
    //             $detail['checkout_date'] = $request->checkout_date;

    //             if($guest['isFileUpload']){
    //                 $temp_file = $guest['id_proof_img'];
    //                 $path = 'public/';
    //                 $oldpath =  $path."temp_images/".$temp_file;
    //                 $storingPath = $path."guest_idproof/".$temp_file;
    //                 Storage::move($oldpath, $storingPath);
    //                 $detail['id_proof_img'] = $guest['id_proof_img'];
    //             }
    //             if(empty($guestDetail)){
    //                 BookingGuestId::create($detail);
    //             }
    //             else{
    //                 BookingGuestId::where('id', $guestDetail->id)->update($detail);
    //             }
    //         }
    //         return response()->json([
    //             'status' => true,
    //             'message'=> "Data saved successfully"
    //         ],200);
    //     }
    //     catch(\Exception $e){
    //         return response()->json([
    //             'status' => false,
    //             'message'=> "Internal Error",
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }


public function savePropertyBookingIds(Request $request){
        try{
            foreach($request->guest_list as $key=>$guest){

                if(isset($guest['id'])){
                    $id = $guest['id'];
                }
                else{
                    $id = '';
                }
                $guestDetail  = BookingGuestId::where(['id'=>$id])->first();
                $detail = array();
                $detail['property_booking_id'] = $request->property_booking_id;
                $detail['name'] = $guest['name'];
                $detail['email'] = $guest['email'];
                $detail['mobile_no'] = $guest['mobile_no'];
                $detail['checkin_date'] = $request->checkin_date;
                $detail['checkout_date'] = $request->checkout_date;
                /* if(isset($guest['dob'])){
                    $detail['dob'] = $guest['dob']?date('Y-m-d', strtotime($guest['dob'])):NULL;
                }
                if(isset($guest['anniversary'])){
                    $detail['anniversary'] = $guest['anniversary']?date('Y-m-d', strtotime($guest['anniversary'])):NULL;
                } */
                // if($guest['isFileUpload']){
                //     $temp_file = $guest['id_proof_img'];
                //     $path = 'public/';
                //     $oldpath =  $path."temp_images/".$temp_file;
                //     $storingPath = $path."guest_idproof/".$temp_file;
                //     Storage::move($oldpath, $storingPath);
                //     $detail['id_proof_img'] = $guest['id_proof_img'];
                // }
                // Handle multiple images
                if (!empty($guest['multipleFiles'])) {
                    $storedImages = $guestDetail ? json_decode($guestDetail->multipleFiles, true) ?? [] : [];

                    foreach ($guest['multipleFiles'] as $key=>$temp_file) {
                        $path = 'public/';
                        $oldpath = $path . "temp_images/" . $temp_file['filename'];
                        $storingPath = $path . "guest_idproof/" . $temp_file['filename'];
                        Storage::move($oldpath, $storingPath);

                        // if (Storage::exists($oldpath)) {
                            $storedImages[] = [
                                'id' => $key,
                                'filename' => $temp_file['filename'],
                                'extension' => $temp_file['extension'],
                                'filepath' => "https://tisya.tempsite.in/storage/guest_idproof/" . $temp_file['filename'],
                            ] ;
                        // }
                    }

                    $detail['id_proof_img'] = json_encode($storedImages);
                }
                if(empty($guestDetail)){
                    BookingGuestId::create($detail);
                }
                else{
                    BookingGuestId::where('id', $guestDetail->id)->update($detail);
                }
            }
            return response()->json([
                'status' => true,
                'message'=> "Data saved successfully"
            ],200);
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message'=> $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function bookingExport(Request $request) {
        return Excel::download(new BookingExport($request->all()), 'bookings.xlsx');
    }


    public function getBookingEnquiry(Request $request){

        try{
           // $list = BookingEnquiry::with(['location', 'property'])->get();
           
            $perPage = $request->has('take') ? $request->get('take') : 20;
             $query = BookingEnquiry::query();
             $query->when(
                 isset($request->checkin_date) && isset($request->checkout_date),
                 function ($q) use ($request) {
                     return $q->where('checkin_date', '>=', $request->checkin_date)
                              ->where('checkout_date', '<=', $request->checkout_date);
                 }
             );
             $query->when(
                 !empty($request->property_name),
                 function ($q) use ($request) {
                     return $q->whereHas('property', function ($subQuery) use ($request) {
                         $subQuery->where('home_name', 'like', '%' . $request->property_name . '%');
                     });
                 }
             );
             
             $list = $query->with(['location', 'property'])
                           ->orderBy('id', 'desc')
                           ->paginate($perPage);
           
            return response()->json([
                'status'=>true,
                'data'=>$list,
                'message' => 'Booking Enquiry Listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function downloadInvoice($id) {
        set_time_limit(0);
        //return view('frontend.invoice');
        $pdf = Pdf::loadView('frontend.invoice');
        return $pdf->stream();
    }


    public function saleReport(Request $request, $role=NULL, $userId=NULL){
        try{
            $list = PropertyBooking::query()
            
                ->when($request->has('searchChannel') && $request->searchChannel !='', function ($query) use ($request) {
                    return $query->where('channel', $request->searchChannel);
                })
                
                ->when($request->has('searchPaymentStatus') && $request->searchPaymentStatus !='', function ($query) use ($request) {
                    return $query->where('property_bookings.property_booking_status', $request->searchPaymentStatus);
                })
                
                
                ->when($request->has('searchPropertyId') && $request->searchPropertyId !='', function ($query) use ($request) {
                    return $query->where('property_id', $request->searchPropertyId);
                })

                ->when($request->has('checkin_date') && $request->has('checkout_date') && $request->checkin_date !='' && $request->checkout_date !='', function ($query) use ($request) {
                    // if ($request->checkin_date) {
                    //     return $query->whereDate('property_bookings.created_at', '>=', $request->checkin_date);
                    // }

                    // if ($request->checkout_date) {
                    //     return $query->whereDate('property_bookings.created_at', '<=', $request->checkout_date);
                    // }
                    
                    if ($request->searchtype == 'checkin') {
                        return $query->whereDate('property_bookings.checkin_date', '>=', $request->checkin_date);
                    }
                    if ($request->searchtype == 'BookingDate') {
                        return $query->whereDate('property_bookings.created_at', '>=', $request->checkin_date);
                    }

                })
                ->where('property_bookings.payable_amount', '>', 0)
                ->where('property_bookings.booking_status', 'paid')
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('paymentRequests')
                ->orderBy('property_bookings.created_at', 'desc')
                ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
            $finalData = array();
            foreach($list as $key=>$value){
                $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $value->id)->where('booking_request_status', 'Payment Received')->sum('amount');
                $guest_detail = $value->customer_detail;
                $detail = array();
                $detail['booking_id'] = $value->booking_id;
                $detail['home_name'] = $value->home_name;
                $detail['location'] = $value->location;
                $detail['guest_email_id'] = $guest_detail['email'];
                $detail['guest_name'] = $guest_detail['first_name'].' '.$guest_detail['last_name'];
                $detail['guest_mobile_no'] = $guest_detail['mobile_number'];
                $detail['channel'] = $value->channel;
                $detail['payable_amount'] = 'INR '.$value->payable_amount;
                $detail['pending_amount'] = 'INR '.($value->payable_amount - $paidAmount);

                if($value->channel !='PMS'){
                    $detail['pending_amount'] = '';
                    $detail['payment_received'] = 'INR '.$value->payable_amount;
                }
                else{
                    $detail['pending_amount'] = ($value->payable_amount - $paidAmount >0)?'INR '.($value->payable_amount - $paidAmount):'';
                    $detail['payment_received'] = ($paidAmount != 0)?'INR '.$paidAmount:"";
                }
                $detail['tax'] = $value->tax? $value->tax."%":'';
                $detail['taxable_amount'] = 'INR '.$value->taxable_amount;
                array_push($finalData, $detail);
            }

            $proeprtyQuery = TblHome::query()
            ->when(isset($role) && $role =='Owner', function ($proeprtyQuery) use ($userId) {
                return $proeprtyQuery->where('user_id', $userId);
            })->with('additionalCharge')->whereNotNull('ru_property_id')->get();

            return response()->json([
                'status'=>true,
                'data'=>$finalData,
                'propertyList'=>$proeprtyQuery,
                'message' => 'Booking Enquiry Listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function saleReportExport(Request $request){
        return Excel::download(new SaleReportExport($request->all()), 'salereport.xlsx');
    }


    public function policeVerificationReport(Request $request){
        try{
            $list = PropertyBooking::query()
                ->when(isset($request->property_id), function ($query) use ($request) {
                    return $query->where('property_id', $request->property_id);
                })
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('paymentRequests')
                ->orderBy('property_bookings.created_at', 'desc')
                ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
            $finalData = array();
            foreach($list as $key=>$value){
                $guest_detail = $value->customer_detail;
                $detail = array();

                $name = $guest_detail['first_name'].' '.$guest_detail['last_name'];
                $email = $guest_detail['email'];
                $mobile_no = $guest_detail['mobile_number'];
                $detail['guest_name'] = $guest_detail['first_name'].' '.$guest_detail['last_name'];
                $detail['guest_email_id'] = $guest_detail['email'];
                $detail['guest_mobile_no'] = $guest_detail['mobile_number'];
                $detail['checkin_date'] = $value->checkin_date;
                $detail['checkout_date'] = $value->checkout_date;

                if(isset($request->name) || isset($request->email) || isset($request->mobile_no)){
                    if(isset($request->name)  && !isset($request->email) && !isset($request->email)){
                        if(str_contains(strtolower($name), strtolower($request->name))){
                            array_push($finalData, $detail);
                        }
                    }
                    if(isset($request->email) && !isset($request->name) && !isset($request->mobile_no)){
                        if($email !=[]){
                            if(str_contains($email, $request->email)){
                                array_push($finalData, $detail);
                            }
                        }
                    }
                    if(isset($request->mobile_no) && !isset($request->name) && !isset($request->email)){
                        if(str_contains($mobile_no, $request->mobile_no)){
                            array_push($finalData, $detail);
                        }
                    }
                    if(isset($request->name) && isset($request->email) && !isset($request->mobile_no)){
                        if($email !=[]){
                            if(str_contains(strtolower($name), strtolower($request->name)) && str_contains($email, $request->email)){
                                array_push($finalData, $detail);
                            }
                        }
                    }
                    if(!isset($request->name) && isset($request->email) && isset($request->mobile_no)){
                        if($email !=[]){
                            if(str_contains(strtolower($email), strtolower($request->email)) && str_contains(strtolower($mobile_no), strtolower($request->mobile_no))){
                                array_push($finalData, $detail);
                            }
                        }
                    }

                    if(isset($request->name) && !isset($request->email) && isset($request->mobile_no)){
                        if(str_contains(strtolower($name), strtolower($request->name)) && str_contains(strtolower($mobile_no), strtolower($request->mobile_no))){
                            array_push($finalData, $detail);
                        }

                    }

                    if(isset($request->name) && isset($request->email) && isset($request->mobile_no)){
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
            return response()->json([
                'status'=>true,
                'data'=>$finalData,
                'message' => 'Booking Enquiry Listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function policeVerificationExport(Request $request){
        return Excel::download(new PoliceVerificationReport($request->all()), 'policereport.xlsx');
    }


    public function cancelBooking($id){
        try {
            $list = PropertyBooking::where("id", $id)->update([
                "property_booking_status" => "Canceled",
            ]);
            $bookingDetail = PropertyBooking::where(["id" => $id])->first();  
            $home = TblHome::where('id', $bookingDetail->property_id)->first();
            unBlockPropertyAvailabilityInRu(
                $home->ru_property_id,
                $bookingDetail->checkin_date,
                $bookingDetail->checkout_date
            );
            return response()->json(
                [
                    "status" => true,
                    "data" => $list,
                    "message" => "Booking Canceled Successfully.",
                ],
                200
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    "status" => false,

                    "message" => $e->getMessage(),
                ],
                500
            );
        }
    }
    public function deletePropertyBookingImage(Request $request)
    {
        try {
            $request->validate([
                'property_booking_id' => 'required',
                'image_id' => 'required',  // The ID of the image to delete
                'main_id' => 'required',  // The ID of the image to delete
            ]);
    
            $propertyBookingId = $request->property_booking_id;
            $imageId = (int) $request->image_id;  // Make sure the image ID is an integer
    
            $bookingGuest = BookingGuestId::where('id',$request->main_id)->where('property_booking_id', $propertyBookingId)->first();
    
            if ($bookingGuest && $bookingGuest->id_proof_img) {
                $idProofImgs = json_decode($bookingGuest->id_proof_img, true);
    
                if (json_last_error() === JSON_ERROR_NONE) {
    
                    $updatedImages = array_filter($idProofImgs, function ($image) use ($imageId) {
                        return (int) $image['id'] !== $imageId;
                    });
    
                    $updatedImages = array_values($updatedImages);
    
                    // If no image was removed (array length didn't change), return an error
                    if (count($updatedImages) === count($idProofImgs)) {
                        return response()->json([
                            'status' => false,
                            'message' => 'No matching image found to delete.'
                        ], 404);
                    }
    
                    // Save the updated images array back to the model
                    $bookingGuest->id_proof_img = json_encode($updatedImages);
                    $bookingGuest->save();
    
                    return response()->json([
                        'status' => true,
                        'message' => 'File deleted successfully.'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to decode the id_proof_img data.'
                    ], 400);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No image found for the provided booking guest.'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    
}
