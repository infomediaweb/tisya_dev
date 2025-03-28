<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblSecondHome;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SecondHomeController extends Controller{

    public function index(Request $request){
        try {
            $query = TblSecondHome::query();

            // Filtering by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Searching by tag line if search parameter is provided
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('tag_line', 'like', $search . '%');
            }

            // Retrieve the first result
            $secondHome = $query->first();

            if($secondHome == NULL)
            {
                $secondHome = '';
            }

            // Add image_path to the result
            if ($secondHome) {
                $secondHome->image_path = '/storage/second_home/';
            }

            return response()->json([
                'status' => true,
                'data' => $secondHome,
                'message' => $secondHome ? 'Successfully retrieved.' : ''
            ], $secondHome ? 200 : 404);

        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request){
        try {
            $data = $request->all();
            // Set default values if not provided in the request
            $data['position'] = $request->position ?? 0;
            $data['status'] = $request->status ?? 1;
            $data['add_ip'] = $request->ip();
            $data['add_by'] = Auth::user()->name;
            // Check if any record exists in the database
            $existingRecord = TblSecondHome::first();
            // If record exists, update it; otherwise, create a new one
            if ($existingRecord) {
                $existingRecord->update($data);
                $secondHome = $existingRecord;
                $message = 'Saved Successfully.';
            }
            else {
                $secondHome = TblSecondHome::create($data);
                $message = 'Saved Successfully.';
            }
            // Image upload and storage for all three images
            $imagePaths = [];
            for ($i = 1; $i <= 3; $i++) {
                $tempFile = $request->{"image$i"};
                $path = 'public/';
                $oldPath = $path . "temp_images/" . $tempFile;
                $storingPath = $path . "second_home/" . $tempFile;
                Storage::move($oldPath, $storingPath);
                $imagePaths[] = $storingPath;
            }
            return response()->json([
                'status' => true,
                'message' => $message,

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

    public function show($id){
        try {
            // Retrieve the first complete record
            $firstCompleteRecord = TblSecondHome::first();

            if ($firstCompleteRecord) {
                return response()->json([
                    'status' => true,
                    'data' => $firstCompleteRecord,
                    'message' => 'retrieved successfully.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No record found.'
                ], 404);
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





public function deleteImage(Request $request){

    // echo "<pre>". $request->fieldName;die();
    try {
        // Validate request parameters
        $request->validate([
            'fieldName' => 'required',
            'imageName' => 'required'
        ]);

        $fieldName = $request->fieldName;
        $imageName = $request->imageName;

        // Find the record with the specified image name
        $secondHome = TblSecondHome::where($fieldName, $imageName)->first();

        // If the record doesn't exist, return an error response
        if (!$secondHome) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found with the specified image name.',
            ], 404);
        }

        // Delete the image file
        $imagePath = "public/second_home/" . $imageName;
        if (!Storage::exists($imagePath)) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found.',
            ], 404);
        }

        if (!Storage::delete($imagePath)) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete the image.',
            ], 500);
        }

        // Update the specified column to an empty value
        $secondHome->$fieldName = '';
        $secondHome->save();

        return response()->json([
            'status' => true,
            'message' => 'Successfully Deleted.',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}





}
