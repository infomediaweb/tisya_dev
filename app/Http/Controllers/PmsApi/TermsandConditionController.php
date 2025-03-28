<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use App\Models\TblAboutUs;
use App\Models\TblAboutInformation;
use App\Models\TblTermsandCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class TermsandConditionController extends Controller{

    public function index(Request $request){
        try {
            $data = TblTermsandCondition::first();
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
                'title_text' => 'required',
                'terms_and_condition' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $aboutUs = new TblTermsandCondition();
            $aboutUs->title_text = $request->title_text;
            $aboutUs->terms_and_condition = $request->terms_and_condition;
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
            $aboutUs = TblTermsandCondition::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'title_text' => 'required',
                'terms_and_condition' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $aboutUs->title_text = $request->title_text;
            $aboutUs->terms_and_condition = $request->terms_and_condition;
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
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id){
        try {
            $aboutUs = TblAboutUs::findOrFail($id);
            // Delete the about us record
            $aboutUs->delete();
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteImage($id) {
        $about_us = TblAboutUs::find($id);
        if(!$about_us) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found.'
            ], 404);
        }
        $about_image_path = "public/about_us/" . $about_us->banner;
        if(!empty($about_us->banner) && Storage::exists($about_image_path)) {
            if(Storage::delete($about_image_path)) {
                $about_us->update(['banner' => '']);
                    // dd($data);
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Deleted.'
                ], 200);
            }
            else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete the image.'
                ], 500);
            }
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Image not found.'
            ], 404);
        }
    }
}
