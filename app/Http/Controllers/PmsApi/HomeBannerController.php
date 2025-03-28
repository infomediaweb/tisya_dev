<?php

namespace App\Http\Controllers\PmsApi;


use App\Http\Controllers\Controller;

use App\Models\TblHomeBanner;
use App\Models\TblHomeBannerImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HomeBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
          

    $banners = TblHomeBanner::with('homeBannerImage', 'homeBannerVideo')
    ->orderBy('position', 'asc') 
    ->orderBy('id', 'asc')  
    ->get();

            if ($banners->isNotEmpty()) {
                // Loop through each banner object and add image_path property
                foreach ($banners as $banner) {
                    //$banner->image_path = $banner->image ? '/storage/home_banner/' . $banner->image : '';
                    $banner->file_path = $banner->file ? '/storage/home_banner/' . $banner->file : '';
                }
    
                return response()->json([
                    'status' => true,
                    'data' => $banners,
                    'message' => 'Successfully retrieved.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => ''
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
    

// public function store(Request $request)
// {
//     try {
//         $title = $request->title;
//         $subtitle = $request->subtitle;
//         $images = $request->images;
//          $position=1;
//         foreach ($images as $image) {
//             $temp_file = $image['filename'];
//             $path = 'public/';
//             $oldpath =  $path."temp_images/".$temp_file;  
//             $storingPath = $path."home_banner/".$temp_file;
//             Storage::move($oldpath, $storingPath);
//             $banner = TblHomeBanner::where('image', $image['filename'])->first();
//             if ($banner) {
//                 // If banner exists, update it
//                 $banner->heading = $title;
//                 $banner->subtitle = $subtitle; 
//                 $banner->position = $position;
//                 $banner->status = $image['status']; 
//                 $banner->add_ip = $request->ip(); 
//                 $banner->add_by = $request->user()->name ?? 'Unknown'; 
//                 $banner->save();
//             } else {
//                 $newBanner = new TblHomeBanner();
//                 $newBanner->image = $image['filename'];
//                 $newBanner->heading = $title;
//                 $newBanner->subtitle = $subtitle;
//                 $newBanner->position = $position;
//                 $newBanner->status = $image['status']; 
//                 $newBanner->add_ip = $request->ip(); 
//                 $newBanner->add_by = $request->user()->name ?? 'Unknown'; 
//                 $newBanner->save();
                
//             } 
//             $position++;
//         }

//         return response()->json(['status' => true, 'message' => 'Saved successfully'], 201);
//     } catch (\Exception $e) {
//         return response()->json(['error' => 'Failed to create banners', 'message' => $e->getMessage()], 500);
//     }
// }
 
 
//  public function store(Request $request)
// {
//     try {
        
//         $temp_file = $request->file;
//         $path = 'public/';
//         $oldpath =  $path . "temp_images/" . $temp_file;
//         $storingPath = $path . "home_banner/" . $temp_file;
//         Storage::move($oldpath, $storingPath);

//         if (TblHomeBanner::exists()) {
//             $newBanner = TblHomeBanner::first();
//         }
//         else {
//             $newBanner = new TblHomeBanner();
//         }

//         $newBanner->file = $request->file;
//         $newBanner->file_type = $request->file_type;
//         $newBanner->heading = $request->title;
//         $newBanner->subtitle = $request->subtitle;
//         $newBanner->apartment_title = $request->apartment_title;
//         $newBanner->position = 1;
//         $newBanner->status = $request->status ?? 1;
//         $newBanner->add_ip = $request->ip(); 
//         $newBanner->add_by = $request->user()->name ?? 'Unknown'; 
//         $newBanner->save();

//         return response()->json([
//             'status' => true,
//             'message' => 'Banner added successfully'
//         ], 201);

//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Internal Error',
//             'error' => $e->getMessage()
//         ], 500);
//     }
// }


public function store(Request $request)
{

   // dd($request);
    try {
        $path = 'public/';

        // Retrieve existing banner or create a new one
        if (TblHomeBanner::exists()) {
            $newBanner = TblHomeBanner::first();
        } else {
            $newBanner = new TblHomeBanner();
        }

        $newBanner->heading = $request->title;
        $newBanner->subtitle = $request->subtitle;
        $newBanner->apartment_title = $request->apartment_title;
        $newBanner->position = 1;
        $newBanner->status = $request->status ?? 1;
        $newBanner->add_ip = $request->ip();
        $newBanner->add_by = $request->user()->name ?? 'Unknown';
        $newBanner->save();

        // Process images
        if (!empty($request->images) && is_array($request->images)) {
            
            foreach ($request->images as $index => $image) {
                
                if (!empty($image['filename'])) {
                    $tempImagePath = $path . "temp_images/" . $image['filename'];
                    $finalImagePath = $path . "home_banner/" . $image['filename'];

                    // Move file from temp to final location
                    Storage::move($tempImagePath, $finalImagePath);

                    // Check if the image already exists
                    $bannerImage = TblHomeBannerImage::where('file', $image['filename'])->first();
                    
                    if ($bannerImage) {

                        // Update existing image record
                        $bannerImage->file_type = $image['file_type']; // Store the file path
                        $bannerImage->position = $index;
                        $bannerImage->status = $image['status'] ?? $bannerImage->status;
                        $bannerImage->save();
                    } else {
                        // Create a new image record
                       if($image['file_type'] == 'video'){
                          TblHomeBannerImage::where('file_type', 'image')->where('banner_id', $newBanner->id)->delete();
                       }
                       if($image['file_type'] == 'image'){
                         TblHomeBannerImage::where('file_type', 'video')->where('banner_id', $newBanner->id)->delete();
                       }
                        TblHomeBannerImage::create([
                            'banner_id' => $newBanner->id,
                            'file' => $image['filename'],
                            'file_type' => $image['file_type'],
                           
                             'position' => $index,
                            'status' => $image['status'] ?? 1,
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Banner and images added successfully',
        ], 201);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage(),
        ], 500);
    }
}


public function savePosition(Request $request)
{
    try {
        // Extract positions array from the request
        $positions = $request->position;

        // Check if $positions is an array
        if (!is_array($positions)) {
            throw new \Exception('$positions must be an array.');
        }

        // Loop through the positions array
        foreach ($positions as $index => $id) {
            // Update the position value for the record with the given id
            TblHomeBanner::where('id', $id)->update(['position' => $index]);
        }

        // Return success response
        return response()->json([
            'status' => true,
            'message' => 'Successfully Updated.'
        ], 200);

    } catch (\Exception $e) {
        // Return error response if an exception occurs
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error' => $e->getMessage()
        ], 500);
    }
}





public function deleteImage($id) {
    //$bannerdata = TblHomeBanner::find($id);
    $banner = TblHomeBannerImage::find($id);
    if(!$banner) {
        return response()->json([
            'status' => false,
            'message' => 'Banner not found.'
        ], 404);
    }

    $banner_image_path = "public/home_banner/" . $banner->file;

    if(!empty($banner->file) && Storage::exists($banner_image_path)) {
        if(Storage::delete($banner_image_path)) {
            $banner->delete(); 
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

public function updateStatus(Request $request, $id)
{
    
    try {
        $banner = TblHomeBanner::findOrFail($id);
      
        

        // Update the status field
        $banner->status = $request->status;

        // Save the changes
        if ($banner->save()) {
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


   
  
}
