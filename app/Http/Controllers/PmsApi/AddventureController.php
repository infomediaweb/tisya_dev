<?php



namespace App\Http\Controllers\PmsApi;





use App\Http\Controllers\Controller;



use App\Models\TblHomeBanner;
use App\Models\HomeFooterBanner;
use App\Models\HomeAddventure;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use DB;


class AddventureController extends Controller

{

    /**

     * Display a listing of the resource.

     */

     public function index()
     {
         try {
            // $data = HomeAddventure::get();
             $data = HomeAddventure::select('addventure-content.*', DB::raw('CONCAT("/storage/home/images/",image) as image_url'),)->get();
             return response()->json([
                 'status' => true,
                 'data' => $data,
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
     


public function saveAddventure(Request $request)
{
    try {
        $addventureContent = $request->addventure_content;
        if (is_array($addventureContent)) {
            foreach ($addventureContent as $item) {
                // Validate the keys exist
                $tempFileName = null;
                if (isset($item['image'])) {
                    $tempFileName = $item['image']; 
                    $oldPath = "public/temp_images/{$tempFileName}";
                    $newPath = "public/home/images/{$tempFileName}";
                    if (Storage::exists($oldPath)) {
                        Storage::move($oldPath, $newPath);
                        //$listContent[$key]['image'] = "{$tempFileName}";
                    }
                }

                if (isset($item['title']) && isset($item['description'])) {
                    $newBanner = HomeAddventure::firstOrNew(['id'=>$item['id']]);
                    $newBanner->title = $item['title'];
                    $newBanner->description = $item['description'];
                    $newBanner->image = $tempFileName;
                    $newBanner->save();
                }
            }
        }
        return response()->json(['status' => true, 'message' => 'Saved successfully'], 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create banners', 'message' => $e->getMessage()], 500);
    }

}



public function deleteAddventure($id=null){
    try{
        HomeAddventure::where('id', $id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Addventure Deleted Successfully'
        ], 200);
    }
    catch(\Exception $e){
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error'=> $e->getMessage()
        ], 500);
    }
  }
  

}

