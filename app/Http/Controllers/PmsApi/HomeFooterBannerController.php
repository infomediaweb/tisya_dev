<?php



namespace App\Http\Controllers\PmsApi;





use App\Http\Controllers\Controller;



use App\Models\TblHomeBanner;
use App\Models\HomeFooterBanner;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use DB;


class HomeFooterBannerController extends Controller

{

    /**

     * Display a listing of the resource.

     */

   public function index()
{
    try {

        $banners = DB::table('home_footer_banners')
        ->select(
            'home_footer_banners.*',
            DB::raw("CONCAT('" . asset('storage/home/images') . "/', image_banner_second) as image_banner_second_show"),
            DB::raw("CONCAT('" . asset('storage/home/images') . "/', image_banner_first) as image_banner_first_show")
        )
        ->get();

        
       // $banners = DB::table('home_footer_banners')->get();
        $banners = $banners->map(function ($banner) {
            $banner->list_content = json_decode($banner->list_content, true);
            if (is_array($banner->list_content)) {
                foreach ($banner->list_content as $key => $content) {
                    if (isset($content['image'])) {
                        $banner->list_content[$key]['image_url'] = asset("storage/home/images/{$content['image']}");
                    }
                }
            }
            return $banner;
        });
        return response()->json([
            'status' => true,
            'data' => $banners,
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


public function saveFooterContent(Request $request)
{
    try {
        
        $title = $request->title;
        $subtitle = $request->subtitle;


        $temp_file = $request->image_banner_first;
        $path = 'public/';
        $oldpath =  $path."temp_images/".$temp_file;  
        $storingPath = $path."home/images/".$temp_file;
        Storage::move($oldpath, $storingPath);


        $temp_file_second = $request->image_banner_second;
        $path = 'public/';
        $oldpath =  $path."temp_images/".$temp_file_second;  
        $storingPath = $path."home/images/".$temp_file_second;
        Storage::move($oldpath, $storingPath);


        $newBanner = new HomeFooterBanner();
        $newBanner->title = $title;
        $newBanner->image_banner_first = $temp_file;
        $newBanner->image_banner_second = $temp_file_second;
        $newBanner->sub_title = $subtitle;
        $newBanner->list_content = json_encode($request->addMultiItem);
        $newBanner->save();
        return response()->json(['status' => true, 'message' => 'Saved successfully'], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create banners', 'message' => $e->getMessage()], 500);
    }

}

public function updateFooterContent(Request $request, $id){
    try {

        $title = $request->title;
        $subtitle = $request->subtitle;
        $update_banner = HomeFooterBanner::findOrFail($id); 
        
        $temp_file = $request->image_banner_first;
        $path = 'public/';
        $oldpath =  $path."temp_images/".$temp_file;  
        $storingPath = $path."home/images/".$temp_file;
        Storage::move($oldpath, $storingPath);
        $update_banner->image_banner_first = $temp_file;



        $temp_file_second = $request->image_banner_second;
        $path = 'public/';
        $oldpath =  $path."temp_images/".$temp_file_second;  
        $storingPath = $path."home/images/".$temp_file_second;
        Storage::move($oldpath, $storingPath);
        $update_banner->image_banner_second = $temp_file_second;


        $listContent = $request->addMultiItem; 
        //dd($listContent);
        if (is_array($listContent)) {
            foreach ($listContent as $key => $item) {
                if (isset($item['image'])) {
                    $tempFileName = $item['image']; 
                    $oldPath = "public/temp_images/{$tempFileName}";
                    $newPath = "public/home/images/{$tempFileName}";
                    if (Storage::exists($oldPath)) {
                        Storage::move($oldPath, $newPath);
                        $listContent[$key]['image'] = "{$tempFileName}";
                    }
                }
            }
        }



        $update_banner->title = $title;
        $update_banner->sub_title = $subtitle;
        $update_banner->list_content = json_encode($listContent);
        $update_banner->save();
        return response()->json(['status' => true, 'message' => 'Saved successfully'], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create banners', 'message' => $e->getMessage()], 500);
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

    $banner = TblHomeBanner::find($id);

    

    if(!$banner) {

        return response()->json([

            'status' => false,

            'message' => 'Banner not found.'

        ], 404);

    }



    $banner_image_path = "public/home_banner/" . $banner->image;



    if(!empty($banner->image) && Storage::exists($banner_image_path)) {

        if(Storage::delete($banner_image_path)) {

            $banner->delete(); // Delete the banner record from the database

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

