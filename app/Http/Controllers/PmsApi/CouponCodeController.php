<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblHomeType;
use App\Models\TblHome;
use App\Models\DiscountCoupon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\Models\DiscountCouponCodeMapping;
use App\helper\MasterHelper;
use App\Models\BookingGuestId;
use App\Exports\GuestDatabaseExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CouponExport;
use DB;

class CouponCodeController extends Controller{

    public function guestDatabaseExport(Request $request) {
        return Excel::download(new GuestDatabaseExport($request->all()), 'guest_database_export.xlsx');
    }

    public function getGuestDatabase(Request $request){
        try {
            DB::statement("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");
            $query = DB::table('booking_guest_ids')
            ->whereNull('booking_guest_ids.deleted_at')
            ->select([
                'booking_guest_ids.id as guest_id',
                'booking_guest_ids.name',
                'booking_guest_ids.email',
                'booking_guest_ids.mobile_no',
                'booking_guest_ids.id_proof_img',
                'booking_guest_ids.checkin_date',
                'booking_guest_ids.checkout_date',
                'booking_guest_ids.status',
                'property_bookings.property_id',
                'tbl_homes.home_name',
                'tbl_homes.home_type',
                'tbl_homes.location_id',
                'tbl_location.location_name',
            ])
            ->leftJoin('property_bookings', 'property_bookings.id', '=', 'booking_guest_ids.property_booking_id')
            ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
            ->leftJoin('tbl_location', 'tbl_location.id', '=', 'tbl_homes.location_id');
        $query->when(!empty($request->name), function ($q) use ($request) {
            return $q->where('booking_guest_ids.name', 'like', '%' . $request->name . '%');
        });
        $query->when(!empty($request->property_name), function ($q) use ($request) {
            return $q->where('tbl_homes.home_name', 'like', '%' . $request->property_name . '%');
        });
        $query->when(!empty($request->email), function ($q) use ($request) {
            return $q->where('booking_guest_ids.email', $request->email);
        });
        
        $query->when(!empty($request->mobile), function ($q) use ($request) {
            return $q->where('booking_guest_ids.mobile_no', $request->mobile);
        });
        //$list = $query->groupBy('booking_guest_ids.email')->get();
        
        $perPage = $request->has('take') ? $request->get('take') : 50;


        $list = $query->groupBy('booking_guest_ids.email')->paginate($perPage);
        
            return response([
                'status' => true,
                'message' => 'Listed Successfully',
                'data' => $list
            ], 200);
        }
        catch (\Exception $e) {
            //dd($e);
            return response([
                'status' => false,
                'message' => 'Error!, please try again later.'
            ], 400);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function getPropertyListByPropertyTypes(Request $request){
        try {
            $query = TblHome::query();
            $ids = $request->get('ids');
            $query->when($ids != "[]", function ($q) use ($ids) {
                return $q->whereIn('id', json_decode($ids));
            });
            $list = $query->whereNotNull('ru_property_id')->get();
            return response([
                'status' => true,
                'message' => 'Listed Successfully',
                'data' => $list
            ], 200);
        }
        catch (\Exception $e) {
            //dd($e);
            return response([
                'status' => false,
                'message' => 'Error!, please try again later.'
            ], 400);
        }
    }

    /**
     * Display the all resource.
     */
    public function list(Request $request){

        //



        try {

            $query = DiscountCoupon::query();

            $query->when(isset($request->title), function ($q) use ($request) {

                return $q->where('title', 'like', "%".$request->title."%");
            });
            $query->when(isset($request->status), function ($q) use ($request) {
                return $q->where('status', $request->status);
            });
            $detail = $query->get();
            if ($detail) {

                return response()->json([

                    'status' => true,

                    'data' => $detail,

                    'message' => 'Successfully Retrive.'

                ], 200);

            } else {

                return response()->json([

                    'status' => false,

                    'message' => 'Something Went Wrong'

                ], 400);

            }

        }

        catch (\Exception $e) {

            return response()->json([

                'status' => false,

                'message' => 'Internal Error'

            ], 500);

        }

    }

    /**
     * Display the specified resource.
     */
    public function show($id=null){
        //
        try {
            $detail = DiscountCoupon::where('id', $id)->first();
            if ($detail) {
                return response()->json([
                    'status' => true,
                    'data' => $detail,
                    'message' => 'Successfully Retrive.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong'
                ], 400);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error'
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     */
    // public function save(Request $request){
    //     try {
    //         $validator = Validator::make($request->all(), [
    //             'title' => 'required',
    //             'start_date' => 'required',
    //             'end_date' => 'required',
    //             'is_offer_valid_only_for_first_time' => 'required',
    //             'user_type' => 'required',

    //             'discount_type' => 'required',
    //             'discount' => 'required',
    //             'generated_coupon_code_by' => 'required'
    //         ]);
    //         if ($validator->fails()) {
    //             return response()->json(
    //                 [
    //                     'status' => false,
    //                     'message' => 'Fields are required',
    //                     'errors' => $validator->errors(),
    //                 ], 422);
    //         }
    //         $detail = DiscountCoupon::firstOrNew(['id'=>$request->id]);
    //         $detail->coupon_code = 'TS00' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    //         $detail->title = $request->title;
    //         $detail->start_date = $request->start_date;
    //         $detail->end_date = $request->end_date;
    //         $detail->is_offer_valid_only_for_first_time = $request->is_offer_valid_only_for_first_time;
    //         $detail->user_type = $request->user_type;
    //         $detail->use_limit = $request->use_limit;
    //         $detail->discount_type = $request->discount_type;
    //         $detail->discount = $request->discount;
    //         $detail->generated_coupon_code_by = $request->generated_coupon_code_by;
    //         $detail->coupon_valid_on_min_no_of_nights = $request->coupon_valid_on_min_no_of_nights;
    //         $detail->stay_date_from = $request->stay_date_from;
    //         $detail->stay_date_to = $request->stay_date_to;
    //         $detail->property_type_id = $request->property_type_id?json_encode($request->property_type_id):NULL;
    //         $detail->property_id = $request->property_id?json_encode($request->property_id):NULL;
    //         $detail->term_and_conditions = $request->term_and_conditions;
    //         $detail->save();
    //         $message = "Coupon code saved successfully";
    //         if($request->id){
    //             $message = "Coupon code updated successfully";
    //         }
    //         return response()->json([
    //             'status' => true,
    //             'message' => $message
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Internal Error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
 public function save(Request $request){

        //

        try {

            $validator = Validator::make($request->all(), [

                'title' => 'required',

                'start_date' => 'required',

                'end_date' => 'required',

                'is_offer_valid_only_for_first_time' => 'required',

                'user_type' => 'required',



                'discount_type' => 'required',

                'discount' => 'required',

                'generated_coupon_code_by' => 'required'

            ]);

            if ($validator->fails()) {

                return response()->json(

                    [

                        'status' => false,

                        'message' => 'Fields are required',

                        'errors' => $validator->errors(),

                    ], 422);

            }



            $detail = DiscountCoupon::firstOrNew(['id'=>$request->id]);

            $detail->coupon_code = 'TS00'.rand();

            $detail->title = $request->title;

            $detail->start_date = $request->start_date;

            $detail->end_date = $request->end_date;

            $detail->is_offer_valid_only_for_first_time = $request->is_offer_valid_only_for_first_time;

            $detail->user_type = $request->user_type;

            $detail->use_limit = $request->use_limit;

            $detail->discount_type = $request->discount_type;

            $detail->discount = $request->discount;

            $detail->generated_coupon_code_by = $request->generated_coupon_code_by;

            $detail->coupon_valid_on_min_no_of_nights = $request->coupon_valid_on_min_no_of_nights;

            $detail->stay_date_from = $request->stay_date_from;

            $detail->stay_date_to = $request->stay_date_to;

            $detail->property_type_id = $request->property_type_id?json_encode($request->property_type_id):NULL;

            $detail->property_id = $request->property_id?json_encode($request->property_id):NULL;

            $detail->term_and_conditions = $request->term_and_conditions;

            if(isset($request->prefix)){

                $detail->prefix = $request->prefix;

            }

            if(isset($request->no_of_coupon_codes)){

                $detail->code = $request->no_of_coupon_codes;

            }

            else{

                $detail->code = $request->coupon_code;

            }

            $detail->save();

            if($request->generated_coupon_code_by =='auto'){

                DiscountCouponCodeMapping::where('discount_coupon_id', $detail->id)->delete();

                for($i = 1; $i<=$request->no_of_coupon_codes; $i++){

                    

                    $code = $request->prefix.rand(10000, 99999);

                    $codeArray = array();

                    $codeArray['code'] = $code;

                    $codeArray['discount_coupon_id'] = $detail->id;

                    DiscountCouponCodeMapping::create($codeArray);

                }

            }

            else{

                $codeArray = array();

                $codeArray['code'] = $request->coupon_code;

                $codeArray['discount_coupon_id'] = $detail->id;

                DiscountCouponCodeMapping::create($codeArray);

            }



            $message = "Coupon code saved successfully";

            if($request->id){

                $message = "Coupon code updated successfully";

            }

            return response()->json([

                'status' => true,

                'message' => $message

            ], 200);

        } catch (\Exception $e) {

            return response()->json([

                'status' => false,

                'message' => 'Internal Error',

                'error' => $e->getMessage()

            ], 500);

        }

    }


    public function updateStatus(Request $request, $id){
        try {
            $data = DiscountCoupon::findOrFail($id);
            $request->validate([
                'status' => 'required|boolean',
            ]);
            $data->status = $request->status;

            if ($data->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Status updated successfully'
                ], 201);
            }
            else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong'
                ], 400);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request){
        //
        try {
            $data = DiscountCoupon::findOrFail($request->get('id'));
            if ($data) {
                $data->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Deleted.'
                ], 200);
            }
            else {
                return response()->json([
                    'status' => false,
                    'message' => 'No record found'
                ], 200);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMultipleRecord(Request $request){
        //
        try {
            $ids = $request->get('ids');
            if(!empty($ids)) {
                DiscountCoupon::whereIn('id', $request->get('id'))->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Successfully deleted selected records.',
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid or empty IDs provided.',
                ], 400);
            }
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function couponCodeExport(Request $request) {

        return Excel::download(new CouponExport($request->all()), 'coupon_code.xlsx');

    }
}
