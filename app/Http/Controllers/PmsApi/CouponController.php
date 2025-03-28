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
use App\helper\MasterHelper;
use DB;
use App\Models\BookingGuestIid;

class CouponController extends Controller{

     /**
     * Display a listing of the resource.
     */
    public function getGuestDatabase(Request $request){
        try {
            $query = BookingGuestIid::query();
            $query->when($request->name != '', function ($q) use ($request) {
                return $q->where('name', 'like', '%'.$request->name.'%');
            });
            $query->when($request->email != '', function ($q) use ($request) {
                return $q->where('email', $request->email);
            });
            $query->when($request->mobile != '', function ($q) use ($request) {
                return $q->where('mobile_no', $request->mobile);
            });
            $list = $query->get();

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
            $query->when($ids != '', function ($q) use ($ids) {
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
    public function list(){
        //
        try {
            $detail = DiscountCoupon::get();
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
    public function save(Request $request){
        //
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'is_offer_valid_only_for_first_time' => 'required',
                'user_type' => 'required',
                'use_limit' => 'required',
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
            $detail->coupon_code = 'VR00'.rand();
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
            $detail->coupon_valid_on_min_total_booking_amount = $request->coupon_valid_on_min_total_booking_amount;
            $detail->stay_date_from = $request->stay_date_from;
            $detail->stay_date_to = $request->stay_date_to;
            $detail->property_type_id = $request->property_type_id;
            $detail->property_id = $request->property_id;
            $detail->term_and_conditions = $request->term_and_conditions;
            $detail->save();
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
}
