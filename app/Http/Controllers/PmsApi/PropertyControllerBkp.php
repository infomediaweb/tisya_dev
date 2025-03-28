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
use App\Exports\BookingExport;
use DB;
use App\Models\RuPropertyAvailability;

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
                    $checkIsAvailable = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', $checkin_date)->first('is_available');
                    $price = 0;
                    if($checkIsAvailable->is_available =='yes'){
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
                        $detail->price = $price;
                        $detail->per_night_price = $price/$date_difference_count;
                        $detail->initial_price = $price;
                        $detail->gst_amount = $gst_amount;
                        $detail->gst_percentage = $gstPrecentage;
                        array_push($filtered_property_list, $detail);
                    }
                }
            }

            $gst_slab = TblGst::get();

            return response()->json([
                'status' => true,
                'data' => array('property_list'=>$filtered_property_list, 'no_of_nights'=>$date_difference_count, 'is_gst_allowed'=>setting()->is_allow_gst, 'states'=>MasterHelper::getStateList(), 'gst_slab'=>$gst_slab),
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
            $query = TblHome::query();
            $query->when($location_id != '', function ($q) use ($location_id) {
                return $q->where('location_id', $location_id);
            });
            $list = $query->with('additionalCharge')->whereNotNull('ru_property_id')->get();
            $filtered_property_list = array();
            if(!empty($list)){
                foreach($list as $detail){
                    $price = 0.00;
                    $per_night_price = 0.00;
                    // $ru_price_detail = DB::table('ru_property_price')->where('ru_property_id', $detail->ru_property_id)->first();
                    // if($ru_price_detail){
                    //     $price = $ru_price_detail->price;
                    //     $per_night_price = $ru_price_detail->price/$date_difference_count;
                    // }
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
                if($checkIsAvailable->is_available =='yes'){
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
                    $detail->price = $price;
                    $detail->per_night_price = $price/$date_difference_count;
                    $detail->initial_price = $price;
                    $detail->gst_amount = $gst_amount;
                    $detail->gst_percentage = $gstPrecentage;
                }
            }
            
            $gst_slab = TblGst::get();

            return response()->json([
                'status' => true,
                'data' => array('propertyDetail'=>$detail, 'no_of_nights'=>$date_difference_count, 'is_gst_allowed'=>setting()->is_allow_gst, 'gst_slab'=>$gst_slab),
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
            $propertyBooking = new PropertyBooking();
            $propertyBooking->location_id = $request->locationId;
            $propertyBooking->property_id = $request->propertyId;
            $propertyBooking->total_amount = round($request->netAmount);
            $propertyBooking->discount_amount = round($request->discount_amount);
            $propertyBooking->payable_amount = round($request->netPayableAmount);
            $propertyBooking->additional_charges = $request->additional_charges?json_encode($request->additional_charges):NULL;
            $propertyBooking->tax_amount = $request->taxAmount;
            $propertyBooking->booking_id = rand(10000000, 99999999);
            $propertyBooking->booking_status = 'pending';
            $propertyBooking->booking_created_by = 'admin';
            $propertyBooking->type = $request->type;
            $propertyBooking['ru_booking_status'] = 'Requested';
            $propertyBooking->no_of_children = $request->no_children;
            $propertyBooking->no_of_adult = $request->no_adult;
            $propertyBooking->customer_detail = json_encode(array('first_name'=>$request->first_name, 'last_name'=>$request->last_name, 'email'=>$request->email_address , 'mobile_number'=>$request->mobile_number));
            $propertyBooking->checkin_date = $request->checkInDate;
            $propertyBooking->checkout_date = $request->checkOutDate;
            $propertyBooking->save();
            
            
            $home  = TblHome::where('id', $request->propertyId)->first();
            blockPropertyAvailabilityInRu($home->ru_property_id, $request->checkInDate , $request->checkOutDate);
            
            //RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($request->checkInDate)), date('Y-m-d', strtotime($request->checkOutDate))])->update(['is_available'=>'no']);
            
                     
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Property Booked Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
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
            ->when(isset($request->checkin_date) && isset($request->checkout_date), function ($query) use ($request) {
                return $query->where('checkin_date', '>=', $request->checkin_date)->where('checkout_date', '<=', $request->checkout_date);
            })
            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('paymentRequests')->orderBy('id', 'desc')->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);

            $finalData = array();
            foreach($list as $key=>$value){
                $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $value->id)->where('booking_request_status', 'Payment Received')->sum('amount');
                $payment_status = 'pending';
                $booking_status = 'not_confirmed';
                if($paidAmount == $value->payable_amount){
                    $payment_status = 'paid';
                    $booking_status = 'confirmed';
                }
                $search_payment_status = '';
                $search_booking_status = '';
                if(isset($request->booking_status)){
                    $search_booking_status = $request->booking_status;
                }
                if(isset($request->payment_status)){
                    $search_payment_status = $request->payment_status;
                }
                if($search_payment_status !='' && $search_booking_status ==''){
                    if($search_payment_status==$payment_status){
                        array_push($finalData, $value);
                    }
                }
                else if($search_payment_status =='' && $search_booking_status !=''){
                    if($search_booking_status==$booking_status){
                        array_push($finalData, $value);
                    }
                }
                else if($search_payment_status !='' && $search_booking_status !=''){
                    if($search_booking_status==$booking_status && $search_payment_status==$payment_status){
                        array_push($finalData, $value);
                    }
                }
                else{
                    array_push($finalData, $value);
                }
            }
            $proeprtyQuery = TblHome::query()
            ->when(isset($role) && $role =='Owner', function ($proeprtyQuery) use ($userId) {
                return $proeprtyQuery->where('user_id', $userId);
            })->with('additionalCharge')->whereNotNull('ru_property_id')->get();
            return response()->json([
                'status' => true,
                'data' => $finalData,
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

    public function getPropertyBookingDetail($id){
        try{
            $bookingDetail = PropertyBooking::leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('bookingGuests')->where('property_bookings.id', $id)->first();

            $bookingDetail->bookingGuestsIds = BookingGuestId::where('property_booking_id', $id)->get();
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

            return response()->json([
                'status' => true,
                'propertyUnavailableDates' => $propertyUnavailableDates,
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


    public function savePropertyBookingIds(Request $request){
        try{
            foreach($request->guest_list as $key=>$guest){
                $guestDetail  = BookingGuestId::where(['id'=>$guest['id']])->first();

                $detail = array();
                $detail['property_booking_id'] = $request->property_booking_id;
                $detail['name'] = $guest['name'];
                $detail['email'] = $guest['email'];
                $detail['mobile_no'] = $guest['mobile_no'];
                $detail['checkin_date'] = $request->checkin_date;
                $detail['checkout_date'] = $request->checkout_date;

                if($guest['isFileUpload']){
                    $temp_file = $guest['id_proof_img'];
                    $path = 'public/';
                    $oldpath =  $path."temp_images/".$temp_file;
                    $storingPath = $path."guest_idproof/".$temp_file;
                    Storage::move($oldpath, $storingPath);
                    $detail['id_proof_img'] = $guest['id_proof_img'];
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
                'message'=> "Internal Error",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bookingExport(Request $request) {
        return Excel::download(new BookingExport($request->all()), 'bookings.xlsx');
    }


    public function getBookingEnquiry(){

        try{
            $list = BookingEnquiry::with(['location', 'property'])->get();
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
}
