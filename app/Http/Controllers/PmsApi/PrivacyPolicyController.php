<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use App\Models\TblAboutUs;
use App\Models\TblAboutInformation;
use App\Models\CancellationPolicy;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class PrivacyPolicyController extends Controller{

    public function index(Request $request){
        try {
            $data = PrivacyPolicy::first();
           // dd($data);
            if ($data) {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Successfully retrieved.'
                ], 200);
            }
            else{
                return response()->json([
                    'status' => false,
                    'message' => '',
                ], 404);
            }
        } catch (Exception $e) {
            // Handle internal server error
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'cancellation_policy' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $aboutUs = new PrivacyPolicy();
            $aboutUs->title = $request->title;
            $aboutUs->cancellation_policy = $request->cancellation_policy;
            $aboutUs->save();
            return response()->json([
                'status' => true,
                'message' => 'Saved Successfully'
            ], 201);
        }
        catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, string $id){
        try {
            $aboutUs = PrivacyPolicy::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'cancellation_policy' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $aboutUs->title = $request->title;
            $aboutUs->cancellation_policy = $request->cancellation_policy;
            $aboutUs->save();
            return response()->json([
                'status' => true,
                'message' => 'Saved Successfully'
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
   
    

    
}
