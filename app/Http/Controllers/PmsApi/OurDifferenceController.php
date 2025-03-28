<?php

namespace App\Http\Controllers\PmsApi;

use Illuminate\Contracts\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblOurDifference;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class OurDifferenceController extends Controller
{
   
    public function index()
    {
        
        try {
            $ourDifferences = TblOurDifference::all();

            if ($ourDifferences->isNotEmpty()) {
                // Loop through each our difference object and add image_path property
                foreach ($ourDifferences as $difference) {
                    $difference->image_path = $difference->image ? '/storage/our_difference/' . $difference->image : '';
                }

            }

                return response()->json([
                    'status' => true,
                    'data' => $ourDifferences,
                    'message' => 'Successfully retrieved.'
                ], 200);
           
                return response()->json([
                    'status' => false,
                    'message' => ''
                ], 404);
            
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
            $differenceData = $request->differenceData;
    
            foreach ($differenceData as $data) {
                // Validate request data
                $validatedData = $request->validate([
                    'differenceData.*.id' => 'nullable|integer', // Make 'id' field optional
                    // 'differenceData.*.title' => 'required|unique:tbl_our_differences,title',
                    // Add other validation rules for other fields if needed
                ]);
    
                // Check if the record already exists
                                $existingDifference = null;
                if (isset($data['id'])) {
                    $existingDifference = TblOurDifference::where('id', $data['id'])
                        ->whereNull('deleted_at')
                        ->first();
                }

                
    
                if ($existingDifference) {

                    $tempFile = $data['image'];
                    $path = 'public/';
                    $oldPath = $path . "temp_images/" . $tempFile;
                    $storingPath = $path . "our_difference/" . $tempFile;
    
                    Storage::move($oldPath, $storingPath);
                    // Update existing record
                    $existingDifference->update([
                        'title' => $data['title'],
                        'detail' => $data['detail'],
                        'image' => $data['image'],
                        'position' => 0,
                        'status' => 1,
                        'add_ip' => $request->ip(),
                        'add_by' => Auth::user()->name
                    ]);
                } else {
                    // Create new record
                    $tempFile = $data['image'];
                    $path = 'public/';
                    $oldPath = $path . "temp_images/" . $tempFile;
                    $storingPath = $path . "our_difference/" . $tempFile;
    
                    Storage::move($oldPath, $storingPath);
    
                    TblOurDifference::create([
                        'title' => $data['title'],
                        'detail' => $data['detail'],
                        'image' => $data['image'],
                        'position' => 0,
                        'status' => 1,
                        'add_ip' => $request->ip(),
                        'add_by' => Auth::user()->name
                    ]);
                }
            }
    
            return response()->json([
                'status' => true,
                'message' => 'Saved successfully.'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Catch validation errors and return them
            return response()->json([
                'status' => false,
                'message' => $e->validator->errors()->first(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    




           
   
    public function deleteImage($id) {
        $difference = TblOurDifference::find($id);
        
        if(!$difference) {
            return response()->json([
                'status' => false,
                'message' => 'Difference not found.'
            ], 404);
        }
    
        $difference_image_path = "public/our_difference/" . $difference->image;
    
        if(!empty($difference->image) && Storage::exists($difference_image_path)) {
            if(Storage::delete($difference_image_path)) {
                $difference->update(['image' => '']); // Remove the image path from the record in the database
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Deleted.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to delete the image.'
                ], 500);
            }
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Image not found.'
            ], 404);
        }
    }
     


    public function deleteRecord($id) {
        $difference = TblOurDifference::find($id);
    
        if (!$difference) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ], 404);
        }
    
        try {
            $difference->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete the record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
