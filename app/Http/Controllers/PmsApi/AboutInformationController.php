<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use App\Models\TblAboutUs;
use App\Models\TblAboutInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AboutInformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   
public function index(Request $request)
{
    try {
        // Query to fetch all records from the about_information table
        $aboutInformation = TblAboutInformation::all();

        return response()->json([
            'status' => true,
            'data' => $aboutInformation,
            'message' => 'Successfully retrieved.'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Show the form for creating a new resource.
     */
   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate request data
            $validator = Validator::make($request->all(), [
                'about_id' => 'required|integer',
                'image' => 'required|string|max:200',
                'title' => 'required|string|max:255',
                'text' => 'required|string',
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
             
            $temp_file = $request->image; //'storage/temp_images/65d09ae3648bd_search.png' -- 

            $path = 'public/';
            
            $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
            $storingPath = $path."about_information/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);
    
            Storage::move($oldpath, $storingPath);
    
            // Create new about information record
            $aboutInformation = new TblAboutInformation();
            $aboutInformation->about_id = $request->about_id;
            $aboutInformation->image = $request->image;
            $aboutInformation->title = $request->title;
            $aboutInformation->text = $request->text;
            $aboutInformation->status = $request->status ?? 1;
            $aboutInformation->add_ip = $request->ip();
            $aboutInformation->add_by = Auth::user()->name; // Assuming you have authentication set up
            $aboutInformation->save();
    
            return response()->json([
                'status' => true,
                'message' => 'successfully Updated'
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    try {
        $aboutInformation = TblAboutInformation::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $aboutInformation,
            'message' => 'Successfully Retrieved.'
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Record not found',
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Find the about information record
            $aboutInformation = TblAboutInformation::findOrFail($id);
    
            // Validate request data
            $validator = Validator::make($request->all(), [
                'about_id' => 'required|integer',
                'image' => 'required|string|max:200',
                'title' => 'required|string|max:255',
                'text' => 'required|string',
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

            $temp_file = $request->image; //'storage/temp_images/65d09ae3648bd_search.png' -- 

            $path = 'public/';
            
            $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
            $storingPath = $path."about_us/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);
    
            Storage::move($oldpath, $storingPath);
    
            // Update the about information record
            $aboutInformation->about_id = $request->about_id;
            $aboutInformation->image = $request->image;
            $aboutInformation->title = $request->title;
            $aboutInformation->text = $request->text;
            $aboutInformation->status = $request->status ?? 1;
            $aboutInformation->update_ip = $request->ip();
            $aboutInformation->update_by = Auth::user()->name; // Assuming you have authentication set up
            $aboutInformation->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Successfully updated'
            ], 200);
    
        } catch (\Exception $e) {
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
      
     public function destroy(string $id)
{
    try {
        // Find the about information record
        $aboutInformation = TblAboutInformation::findOrFail($id);

        // Delete the about information record
        $aboutInformation->delete();

        return response()->json([
            'status' => true,
            'message' => 'Successfully Deleted.'
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Record not found',
        ], 404);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function deleteImage($id) {
    $about_info = TblAboutInformation::find($id);
    
    if(!$about_info) {
        return response()->json([
            'status' => false,
            'message' => 'Image not found.'
        ], 404);
    }

    $about_image_path = "public/about_information/" . $about_info->image;


    if(!empty($about_info->image) && Storage::exists($about_image_path)) {
        if(Storage::delete($about_image_path)) {
           $about_info->update(['image' => '']);
            // dd($data);
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
     
  public function singleDelete($id)
  {
      try {
          // Find the record by ID
          $aboutInformation = TblAboutInformation::findOrFail($id);
          
          // Delete the record
          $aboutInformation->delete();
  
          return response()->json([
              'status' => true,
              'message' => 'Successfully Deleted'
          ], 200);
  
      } catch (\Exception $e) {
          return response()->json([
              'status' => false,
              'message' => 'Failed to delete record.',
              'error' => $e->getMessage(),
          ], 500);
      }
  }
   

}
