<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblJoinOurNetworkIntro;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
class JoinOurNetworkIntroController extends Controller
{
    
    
    public function index(Request $request)
{
    try {
        $query = TblJoinOurNetworkIntro::query();
        
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
        $joinOurNetworkIntro = $query->first();

        if($joinOurNetworkIntro == NULL)
        {
            $joinOurNetworkIntro = '';
        }

        // Add image_path to the result
        if ($joinOurNetworkIntro) {
            $joinOurNetworkIntro->image_path = '/storage/join_our_network_intro/';
        }
       
        return response()->json([
            'status' => true,
            'data' => $joinOurNetworkIntro,
            'message' => $joinOurNetworkIntro ? 'Successfully retrieved.' : ''
        ], $joinOurNetworkIntro ? 200 : 404);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
   

public function store(Request $request)
{
    try {
        $data = $request->all();

        // Set default values if not provided in the request
        $data['position'] = $request->position ?? 0;
        $data['status'] = $request->status ?? 1;
        $data['add_ip'] = $request->ip();
        $data['add_by'] = Auth::user()->name;

        // Check if any record exists in the database
        $existingRecord = TblJoinOurNetworkIntro::first();

        // If record exists, update it; otherwise, create a new one
        if ($existingRecord) {
            $existingRecord->update($data);
            $joinOurNetworkIntro = $existingRecord;
            $message = 'Saved Successfully.';
        } else {
            $joinOurNetworkIntro = TblJoinOurNetworkIntro::create($data);
            $message = 'Saved Successfully.';
        }

        // Image upload and storage for all three images
        $imagePaths = [];
        for ($i = 1; $i <= 3; $i++) {
            $tempFile = $request->{"image$i"};
            $path = 'public/';
            $oldPath = $path . "temp_images/" . $tempFile;
            $storingPath = $path . "join_our_network_intro/" . $tempFile;
            Storage::move($oldPath, $storingPath);
            $imagePaths[] = $storingPath;
        }

        return response()->json([
            'status' => true,
            'message' => $message,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function show($id)
{
    try {
        // Retrieve the record by ID
        $record = TblJoinOurNetworkIntro::find($id);

        if ($record) {
            return response()->json([
                'status' => true,
                'data' => $record,
                'message' => 'Retrieved successfully.'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No record found.'
            ], 404);
        }

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function deleteImage(Request $request)
    {
        try {
            // Validate request parameters
            $request->validate([
                'fieldName' => 'required',
                'imageName' => 'required'
            ]);
    
            $fieldName = $request->fieldName;
            $imageName = $request->imageName;
    
            // Find the record with the specified image name
            $joinOurNetworkIntro = TblJoinOurNetworkIntro::where($fieldName, $imageName)->first();
    
            // If the record doesn't exist, return an error response
            if (!$joinOurNetworkIntro) {
                return response()->json([
                    'status' => false,
                    'message' => 'Record not found with the specified image name.',
                ], 404);
            }
    
            // Delete the image file
            $imagePath = "public/join_our_network_intro/" . $imageName;
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
            $joinOurNetworkIntro->$fieldName = '';
            $joinOurNetworkIntro->save();
    
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
