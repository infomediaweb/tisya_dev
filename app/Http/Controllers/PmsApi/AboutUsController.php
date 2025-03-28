<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use App\Models\TblAboutUs;
use App\Models\TblAboutInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class AboutUsController extends Controller{

    public function index(Request $request){
        try {
            // Retrieve the data with eager loading
            $data = TblAboutUs::with('aboutInformation')->first();
            // Check if data exists
            if ($data) {
                // Modify the banner path
                $data->banner_path = $data->banner ? "/storage/about_us/".$data->banner : '';
                // Loop through each aboutInformation record
                foreach ($data->aboutInformation as $about) {
                    $about->image_path = $about->image ? "/storage/about_information/" . $about->image : '';
                }
                // Return the modified data in the response
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
            $multiServices = $request->input('multiServices');
            $validator = Validator::make($request->all(), [
                'banner' => 'required|string|max:200',
                'multiServices' => 'required|array',
                'multiServices.*.image' => 'required',
                'multiServices.*.title' => 'required|string|max:200',
            ]);
            // Return validation errors if any
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $temp_file = $request['banner'];
            $path = 'public/';
            $oldpath =  $path."temp_images/".$temp_file;
            $storingPath = $path."about_us/".$temp_file;
            $res = Storage::move($oldpath, $storingPath);
            // Check if any records exist in TblAboutUs
            if (TblAboutUs::exists()) {
                // If records exist, update the first record
                $aboutUs = TblAboutUs::first();
            }
            else {
                // If no records exist, create a new record
                $aboutUs = new TblAboutUs();
            }
            $aboutUs->banner = $request->banner;
            $aboutUs->banner_text = $request->banner_text;
            $aboutUs->about_content = $request->about_content ?? '';
            $aboutUs->service_content = $request->service_content ?? '';
            $aboutUs->status = $request->status ?? 1;
            $aboutUs->add_ip = $request->ip();
            $aboutUs->add_by = Auth::user()->name; // Assuming you have authentication set up
            $aboutUs->save();
            $about_id = $aboutUs->id;
            //===================================================================//
            foreach ($multiServices as $multiService) {
                // Check if the "id" key exists in the current $multiService element
                if (isset($multiService['id'])) {
                    // If "id" exists, fetch the corresponding record for updating
                    $aboutInformation = TblAboutInformation::find($multiService['id']);
                }
                else {
                    // If "id" doesn't exist, create a new record
                    $aboutInformation = new TblAboutInformation();
                }
                // Your existing code for updating or creating records goes here
                $temp_file = $multiService['image'];
                $oldpath =  $path."temp_images/".$temp_file;
                $storingPath = $path."about_information/".$temp_file;
                Storage::move($oldpath, $storingPath);
                $aboutInformation->about_id = $about_id;
                $aboutInformation->image = $multiService['image'];
                $aboutInformation->title = $multiService['title'];
                $aboutInformation->text  = $multiService['text'] ?? '';
                $aboutInformation->status = $request->status ?? 1;
                $aboutInformation->add_ip = $request->ip();
                $aboutInformation->add_by = Auth::user()->name; // Assuming you have authentication set up
                $aboutInformation->save();
            }
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
            $aboutUs = TblAboutUs::findOrFail($id);
            // Validate request data
            $validator = Validator::make($request->all(), [
                'banner' => 'required|string|max:200',
                'banner_text' => 'required|string|max:200',
                'about_content' => 'required|string',
                'service_content' => 'required|string',
                'status' => 'boolean',
            ]);
            // Return validation errors if any
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            $temp_file = $request->banner; //'storage/temp_images/65d09ae3648bd_search.png' --
            $path = 'public/';
            $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
            $storingPath = $path."about_us/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);
            Storage::move($oldpath, $storingPath);
            // Update the about us record
            $aboutUs->banner = $request->banner;
            $aboutUs->banner_text = $request->banner_text;
            $aboutUs->about_content = $request->about_content;
            $aboutUs->service_content = $request->service_content;
            $aboutUs->status = $request->status ?? 1;
            $aboutUs->update_ip = $request->ip();
            $aboutUs->update_by = Auth::user()->name;
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
