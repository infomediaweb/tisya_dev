<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblSitesetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use DB;


class SitesettingController extends Controller{
    /**
     * Display a listing of the resource.
     */
    public function index(){
        try{
            $data = TblSitesetting::all();
            if ($data) {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Successfully Retrive.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){

        // $validator = validator::make($request->all(),[
        //     'website_markup' => 'required',
        // ]);
        // if($validator->fails()){
        //     return response()->json(
        //         [
        //             'status'=> false,
        //             'message' => 'Fields are required',
        //             'errors' => $validator->errors(),
        //         ], 422);
        // }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id){

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id){
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){

    }

    public function setWebsiteMarkup(Request $request){
        // $data = TblSitesetting::first();
        // return $data->website_markup;
        try{

            $validator = Validator::make($request->all(), [
                'website_markup' => '',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ], 422);
            }

            $data = TblSitesetting::first();
            $data->website_markup = $request->website_markup;
            $data->save();

            return response()->json([
                'status' => true,
                'message' => 'Successfully Updated.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function changeGstAllowSetting(Request $request){
        try{
            $data = TblSitesetting::first();
            $data->is_allow_gst = $data->is_allow_gst==0?1:0;
            $data->save();
            return response()->json([
                'status' => true,
                'message' => 'GST Setting Changed Successfully.'
            ], 200);
        }
        catch(\Exception $e) {
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
    public function destroy(string $id){
        //
    }
}
