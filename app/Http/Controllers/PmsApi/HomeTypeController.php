<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblHomeType;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use DB;

class HomeTypeController extends Controller{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        //
        try {
            $query = TblHomeType::query();
            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('name', 'like', $search . '%');
            }
            // Take the counting of per page data
            $perPage = $request->has('take') ? $request->get('take') : 20;
            // convert these data into pagination
            $data = $query->paginate($perPage);
            // return $data->items();
            // return $data->url($page);
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Successfully Retrive'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function store(Request $request){
        try {
            // return $request->all();
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ], 422);
            }
            $checkDuplicate = TblHomeType::where('name', '=', $request->name)->get();
            if($checkDuplicate->count() > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'Already added',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $SQL = new TblHomeType();
            $SQL->name = $request->name;
            $SQL->url_key = Str::of($request->input('name'))->slug('-');
            $SQL->meta_title = !empty($request->meta_title) ? $request->meta_title : $request->name;
            $SQL->meta_description = !empty($request->meta_description) ? $request->meta_description : $request->name;
            $SQL->meta_keyword = !empty($request->meta_keywords) ? $request->meta_keywords : $request->name;
            $SQL->status = $request->status;
            $SQL->add_ip = $request->ip();
            $SQL->add_by = Auth::user()->name;
            $SQL->save();
            return response([
                'status' => true,
                'message' => 'Added Successfully'
            ], 200);
        }
        catch (\Exception $e) {
            //dd($e);
            return response([
                'status' => false,
                'message' => 'Error!, please try again later.'
            ], 400);
        }
        return response([
            'status' => false,
            'message' => 'Internal error, please try again later.'
        ], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id){
        //
        try {
            $data = TblHomeType::find($id);
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id){
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id){
        //
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ], 422);
            }
            $checkDuplicate = TblHomeType::where('name', '=', $request->name)->where('id','!=',$id)->get();
            if($checkDuplicate->count() > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'The name has already been taken',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $SQL = TblHomeType::findOrFail($id);
            $SQL->name             = $request->name;
            $SQL->url_key          = Str::of($request->input('name'))->slug('-');
            $SQL->meta_title       = !empty($request->meta_title) ? $request->meta_title : $request->name;
            $SQL->meta_description = !empty($request->meta_description) ? $request->meta_description : $request->name;
            $SQL->meta_keyword     = !empty($request->meta_keywords) ? $request->meta_keywords : $request->name;
            $SQL->status           = $request->status;
            $SQL->update_ip        = $request->ip();
            $SQL->update_by        = Auth::user()->name;
            $SQL->save();
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


    public function updateStatus(Request $request, $id){
        try {
            $data = TblHomeType::findOrFail($id);
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
    public function destroy(string $id){
        //
        try {
            $data = TblHomeType::findOrFail($id);
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
                TblHomeType::whereIn('id', $ids)->delete();

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

    public function getHomeType() {
        try
        {
             $hoemTypeData = MasterHelper::getHomeType();

             return response()->json([
                 'status' => true,
                 'data' => $hoemTypeData,
                 'message' => 'Sucess'
             ], 200);

         } catch (\Exception $e) {
             return response()->json([
                 'status' => 'failed',
                 'message' => 'Internal Error'
             ], 500);
         }

     }





}
