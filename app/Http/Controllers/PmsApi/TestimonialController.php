<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblTestimonial;
use App\Models\TblHome;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class TestimonialController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Start with the base query
            $query = TblTestimonial::orderBy('position', 'asc');
            
            // Filtering by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Searching by guest_name if search parameter is provided
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('guest_name', 'like', $search . '%');
            }
            
            // Retrieve all results without pagination
            $testimonials = $query->get();
    
            foreach ($testimonials as $testimonial) {
                $testimonial->file_path = $testimonial->file ? '/storage/testimonials/' . $testimonial->file : '';
    
                $home = TblHome::find($testimonial->home_name);
                if ($home) {
                    $testimonial->home_name = $home->home_name;
                } else {
                    $testimonial->home_name = null;
                }
            }
    
            return response()->json([
                'status' => true,
                'data' => $testimonials,
                'message' => 'Testimonials retrieved successfully.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Server Error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    


    public function store(Request $request)
{
    try {
      
        // Check for duplicates based on guest name (if needed)
        // $checkDuplicate = TblTestimonial::where('guest_name', '=', $request->guest_name)->first();
        
        $maxPosition = TblTestimonial::max('position');
        $position = $maxPosition !== null ? $maxPosition + 1 : 0;

        // if (!is_null($checkDuplicate)) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Testimonial with this guest name already exists'
        //     ], 422);
        // }

        $temp_file = $request->file;
        $path = 'public/';
        $oldpath =  $path . "temp_images/" . $temp_file;
        $storingPath = $path . "testimonials/" . $temp_file;

        Storage::move($oldpath, $storingPath);

        // Create new testimonial record
        $testimonial = new TblTestimonial();
        $testimonial->guest_name = $request->guest_name;
        $testimonial->home_name = $request->home_name;
        $testimonial->file = $request->file;
        $testimonial->file_type = $request->file_type;
        $testimonial->date = $request->date;
        $testimonial->headline = $request->headline;
        $testimonial->position = $position ?? 0;
        $testimonial->status = $request->status ?? 1;
        $testimonial->add_ip = $request->ip();
        $testimonial->add_by = Auth::user()->name;
        $testimonial->save();

        return response()->json([
            'status' => true,
            'message' => 'Testimonial added successfully'
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function show(string $id)
{
    try {
        // Find the testimonial by its ID
        $testimonial = TblTestimonial::findOrFail($id);  

        // Add path to the image value
        $testimonial->file_path = $testimonial->file ? '/storage/testimonials/' . $testimonial->file : '';

        // Find the corresponding TblHome record based on home_id
        $home = TblHome::find($testimonial->home_name);
        
        // If a corresponding TblHome record is found, assign its name to the home_name property
        if ($home) {
            $testimonial->home_name = $home->home_name;
        } else {
            // If no corresponding TblHome record is found, set home_name to null or any other default value as needed
            $testimonial->home_name = null;
        }

        return response()->json([
            'status' => true,
            'data' => $testimonial,
            'message' => 'Successfully Retrieved.'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function update(Request $request, string $id)
    {
        try {
            $updateSQL = TblTestimonial::findOrFail($id);
    
            $temp_file = $request->file;
            $path = 'public/';
            $oldpath =  $path . "temp_images/" . $temp_file;
            $storingPath = $path . "testimonials/" . $temp_file;
    
            Storage::move($oldpath, $storingPath);
    
            // Update the testimonial record
            $updateSQL->guest_name = $request->guest_name;
            $updateSQL->home_name = $request->home_name;
            $updateSQL->file = $request->file;
            $updateSQL->file_type = $request->file_type;
            $updateSQL->date = $request->date;
            $updateSQL->headline = $request->headline;
            // $updateSQL->position = $request->position ?? 0;
            // $updateSQL->status = $request->status ?? 1;
            $updateSQL->update_ip = $request->ip();
            $updateSQL->update_by = Auth::user()->name;
            $updateSQL->save();
    
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
    
    public function destroy(string $id)
    {
        try {
            $testimonial = TblTestimonial::findOrFail($id);
    
            // Delete the testimonial record
            $testimonial->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function deleteMultipleRecord(Request $request)
{
    try {
        $ids = $request->get('ids');
        
        if (!empty($ids)) {
            TblTestimonial::whereIn('id', $ids)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Successfully deleted selected records.',
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid or empty IDs provided.',
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}


public function updateStatus(Request $request, $id)
{
    try {
        $testimonial = TblTestimonial::findOrFail($id);
      
        $request->validate([
            'status' => 'required|boolean',
        ]);

        // Update the status field
        $testimonial->status = $request->status;

        // Save the changes
        if ($testimonial->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully'
            ], 201);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 400);
        }
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function deletefile($id) {
    $testimonial = TblTestimonial::find($id);
    
    if(!$testimonial) {
        return response()->json([
            'status' => false,
            'message' => 'Testimonial not found.'
        ], 404);
    }

    $testimonial_file_path = "public/testimonials/" . $testimonial->file;

    if(!empty($testimonial->file) && Storage::exists($testimonial_file_path)) {
        if(Storage::delete($testimonial_file_path)) {
           $testimonial->update(['file' => '']);
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete the file.'
            ], 500);
        }
    } else {
        return response()->json([
            'status' => false,
            'message' => 'file not found.'
        ], 404);
    }
}



public function savePosition(Request $request)
{
    try {
        $positions = $request->position;
        
        // Check if $positions is an array
        if (!is_array($positions)) {
            throw new \Exception('$positions must be an array.');
        }

        foreach ($positions as $index => $id) {
            TblTestimonial::where('id', $id)->update(['position' => $index]);
        }

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



    
}
