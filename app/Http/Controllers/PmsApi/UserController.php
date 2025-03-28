<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use App\Models\TblHome;
use App\Models\TblHomeType;
use App\Models\TblState;
use App\Models\TblLocation;
use App\Models\TblHomeImageVideo;
use App\Models\TblFeatures;
use App\Models\TblHomeAmenities;
use App\Models\TblHomeReview;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Laravel\Prompts\Table;
use App\Http\Services\HomeServices;
use App\Models\HomeAdditionalCharge;
use App\Models\User;

class UserController extends Controller{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        try{
           // $list = User::where('id', '!=', '1')->get();
            // $query->with('homeImageVideo','homeFeatures');

            $perPage = $request->has('take') ? $request->get('take') : 15;

            $list = User::where('id', '!=', '1')->paginate($perPage);

            return response()->json([
                'status' => true,
                'data' => $list,
                'message' => 'Successfully Retrive'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        try{
            $validator = Validator::make($request->all(), [
               'name' => 'required',
               'email' => 'required',
               'mobile_no' => 'required',
               'role' => 'required',

               'password' => 'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Fields are required',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            if (User::where('email', $request->email)
                    ->where('id', '!=', $request->id)
                    ->exists()) {
                return response()->json([
                    'status' => false,
                    'message' => 'User email has already been taken',
                ], 422);
            }

            $obj = User::firstOrNew(['id'=>$request->id]);
            $obj->name = $request->name;
            $obj->email = $request->email;
            $obj->mobile_no = $request->mobile_no;

            $obj->password = $request->password;
            $obj->pass = $request->password;
            $obj->role = $request->role;
            $obj->save();

            if(isset($request->mappedProperties) && $request->mappedProperties && $request->role=='Owner'){
                TblHome::where('user_id', $obj->id)->update(['user_id'=>NULL]);
                foreach($request->mappedProperties as $mapped_property){
                    TblHome::where('id', $mapped_property)->update(['user_id'=>$obj->id]);
                }
            }
            return response()->json([
                'status' => true,
                'message' => 'User Detail Saved Successfully'
            ], 200);

            return response([
                'status' => true,
                'message' => 'Added Successfully',
                'last_insert_id' => $SQL->id
            ], 200);

        }catch(\Exception $e) {
            return response([
                'status' => false,
                'message' => 'Error!, please try again later.',
                'error' => $e->getMessage()
            ], 400);
        }

        return response([
            'status' => false,
            'message' => 'Internal error, please try again later.'
        ], 401);
    }

    public function detail($id=NULL){
        try{
            $detail = User::where('id', $id)->first();
            $query = TblHome::query();
            $query->when($id != '', function ($q) use ($id) {
                return $q->where('user_id', $id);
            });
            $homeList = $query->orWhereNull('user_id')->whereNotNull('ru_property_id')->get();
            return response()->json([
                'status' => true,
                'data' =>$detail,
                'propertyList'=>$homeList,
                'message' => 'Listed successfully'
            ], 201);

        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updateStatus(Request $request, $id){

        try{
            $query = User::findOrFail($id);
            $request->validate([
                'status'=> 'required|boolean',
            ]);

            $query->status = $request->status;
            if ($query->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Status updated successfully'
                ], 201);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong'
                ], 400);
            }

        }catch(\Exception $e){
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
        try{
            $query = User::findOrFail($id);
            if ($query) {
                $query->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'User Deleted Successfully.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No record found'
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMultipleRecord(Request $request){
        try{
            $ids = $request->get('ids');
            if(!empty($ids)){
                User::whereIn('id', $ids)->delete();
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
        }
        catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message'=> 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveGallery(Request $request){
    //    return $request->filename;
        try{

        if(!empty($request->filename)){
        //    dd($request->filename);

            $home_id = $request->home_id;
            $type = $request->type;

            $deleteimgsql = TblHomeImageVideo::where(['home_id'=> $home_id, 'type'=> $type])->delete();
            // dd($deleteimgsql);
            foreach($request->filename as $index => $objImg){


                // $title = $objImg['title'];
                $title = isset($objImg['title']) ? $objImg['title'] : null;

                $defaultImage = isset($objImg['default']) ? (int)$objImg['default'] : 0;

                    $temp_file = $objImg['filename'];
                    // $newFileName = $home_id . "-home-" .  . "-" . $objImg['filename'];
                    $newFileName = (strpos($temp_file, $home_id."-home-") !== false) ? $temp_file : $home_id . "-home-" . $temp_file;

                    $temp_filepath = $objImg['filepath'];

                    $path = 'public/';

                    $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
                    $storingPath = $path."home/images/".$newFileName; //str_replace('temp_images/', 'amenities/', $oldpath);
                    Storage::move($oldpath, $storingPath);

                    $Image_SQL               = new TblHomeImageVideo();
                    $Image_SQL->home_id      = $home_id;
                    $Image_SQL->type         = $type;
                    $Image_SQL->title        = $title;
                    $Image_SQL->filename     = $newFileName;
                    // $Image_SQL->filepath     = $storingPath;
                    $Image_SQL->default      = $defaultImage;
                    $Image_SQL->position     = $index;
                    $Image_SQL->add_ip       = $request->ip();
                    $Image_SQL->add_by       = Auth::user()->name;
                    $Image_SQL->save();

            }

            return response([
                'status' => true,
                'message' => 'Added Successfully'
            ], 200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong'
            ], 400);
        }
       }catch(\Exception $e){
            return response([
                'status' => false,
                'message' => 'Error!, please try again later.',
                'error' => $e->getMessage()
            ], 400);
       }
    }

    public function saveVideo(Request $request){
       try{
          if($request->filename){
            $home_id = $request->home_id;
            $type = $request->type;

            // $deleteVideoSql = TblHomeImageVideo::where(['home_id'=> $home_id, 'type'=> $type])->delete();

            $temp_file = $request->filename;
            $temp_filepath = $request->filepath;

            $path = 'public/';
            $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
            $storingPath = $path."home/video/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);
            Storage::move($oldpath, $storingPath); // move the file to newpath
            // Storage::copy($oldpath, $newPath);

            //get the new filename for store into database and read the file via url
            $newPath = 'storage/home/video/'.$temp_file; //str_replace('public/', 'storage/', $storingPath);

            $videoSQL            = new TblHomeImageVideo();
            $videoSQL->home_id   = $home_id;
            $videoSQL->type      = $type;
            $videoSQL->filename  = $temp_file;
            $videoSQL->add_ip    = $request->ip();
            $videoSQL->add_by    = Auth::user()->name;
            $videoSQL->save();
            return response()->json([
                'status' => true,
                'message' => 'Added Successfully',
            ], 200);
          }else {
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong'
            ], 400);
          }
       }catch(\Exception $e){
        return response()->json([
          'status' => false,
          'message' => 'Error!, please try again later.',
          'error' => $e->getMessage()
        ],500);
       }
    }

    public function saveVideoPosition(Request $request){
        try{
                $SQL = TblHomeImageVideo::where(['home_id'=> $request->home_id, 'type'=> $request->type])->whereIn('id', $request->position)->get();
                foreach($SQL as $k => $v){
                    TblHomeImageVideo::where('id', $v->id)->update(['position' => array_search($v->id, $request->position)]);
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Updated.',
                ], 200);

        }catch(\Exception $e){
            return response()->json([
               'status' => false,
               'mesage' => 'Internal Error',
               'error' => $e->getMessage()
            ], 500);
        }
    }

  public function saveFeatures(Request $request){
    try{
         if(!empty($request->features)){
            $home_id = $request->home_id;
            if($request->features_heading){
                $tblHomeData = TblHome::where('id',  $home_id)->first();
                $tblHomeData->features_heading = $request->features_heading;
                $tblHomeData->save();
            }

            $deleteFeatures = TblFeatures::where('home_id', $home_id)->delete();

            foreach($request->features as $index=>$objFeatures){
              $featuresSQL = new TblFeatures();
              $featuresSQL->home_id      = $home_id;
              $featuresSQL->title        = $objFeatures['title'];
              $featuresSQL->detail	     = $objFeatures['detail'];
              $featuresSQL->position     = $index;
              $featuresSQL->add_ip       = $request->ip();
              $featuresSQL->add_by       = Auth::user()->name;
              $featuresSQL->save();

            }
          return response()->json([
            'status' => true,
            'message' => 'Added Successfully'
          ], 200);
         }else {
            return response()->json([
              'status'=>false,
              'message'=> 'Something Went Wrong'
            ], 400);
         }
    }catch(\Exception $e){
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error'=> $e->getMessage()
        ], 500);
    }
  }

    public function showAllAmenities(Request $request, string $id) {
        try{
            // $finalArray = [];
            $allData = MasterHelper::getAmenities();

            // foreach($allData as $key => $value){
            //     $tbl_home_amenities_detail = MasterHelper::checkedAmenities($value['id'], $id);
            //     $value['home_amenities_detail'] = !empty($tbl_home_amenities_detail)?$tbl_home_amenities_detail:[];

            //     array_push($finalArray, $value);
            // }
            if($allData){
                return response()->json([
                'status' => true,
                'message' => 'Successfully Retrive Data',
                'data' => $allData
                ], 200);
            }else {
                return response()->json([
                    'status' => false,
                'message' => 'Something went wrong',
                ], 400);
            }
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveAmenities(Request $request){
       try{
           $home_id = $request->home_id;
           $amenities = $request->amenities;
           TblHomeAmenities::where('home_id', $home_id)->delete();

           foreach($amenities as $key => $objAmenities){
              $amenities_id = $objAmenities['amenities_id'];
              $amenities_name = $objAmenities['amenities_name'];
              $amenities_number = $objAmenities['amenities_number'];

              $SQL                   = new TblHomeAmenities();
              $SQL->home_id          = $home_id;
              $SQL->amenities_id     = $amenities_id;
              $SQL->amenities_name   = $home_id;
              $SQL->amenities_number = $amenities_number;
              $SQL->position         = $key;
              $SQL->add_ip           = $request->ip();
              $SQL->add_by           = Auth::user()->name;
              $SQL->save();

           }
            return response()->json([
                'status' => true,
                'message' => 'Added Successfully'
            ], 200);

       }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
       }
    }

    public function saveReviews(Request $request, $id = null) {

        try{
           $SQLReview = TblHomeReview::firstOrNew(['id'=>$id]);
           $validator = validator::make($request->all(), [
               'guest_name' => 'required',
               'review_date' => 'required',
               'rating' => 'required',
               'comment' => 'required',
           ]);
           if($validator->fails()){
             return response()->json([
                'status' => false,
                'message' => 'Fields are required',
                'error' => $validator->errors()
             ], 422);
           }

        //    $SQLReview              = new TblHomeReview();
           $SQLReview->home_id     = $request->home_id;
           $SQLReview->guest_name  = $request->guest_name;
           $SQLReview->review_date = $request->review_date;
           $SQLReview->rating      = $request->rating;
           $SQLReview->comment     = $request->comment;
           $SQLReview->add_ip      = $request->ip();
           $SQLReview->add_by      = Auth::user()->name;
           $SQLReview->save();
            return response()->json([
                'status' => true,
                'message' => 'Added Successfully'
            ], 200);
        }catch(\Exception $e) {
            return response()->json([
                'status' => false,
                'message'=> 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showHomeReviews(Request $request, string $id){
        try{
            $data = TblHomeReview::find($id);

            if($data) {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Successfully Retrive.'
                ], 200);
            }
            else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong'
                ], 400);
            }

        }catch(\Exception $e){
            return response()->json([
              'status' => false,
              'message' => 'Internal Error',
              'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteReviews(Request $request){
        try{
            $home_id = $request->home_id;
            $reviews_id = $request->id;
            $delete = TblHomeReview::where(['id' => $reviews_id, 'home_id' => $home_id])->delete();

            return response()->json([
                'status' => true,
                'message'=> "Successfully Deleted"
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message'=> "Internal Error",
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteMultipleReviews(Request $request){
        try{
             $IDs = $request->get('ids');
             if(!empty($IDs)){
                TblHomeReview::whereIn('id', $IDs)->delete();
                    return response()->json([
                        'status' => true,
                        'message'=> "Successfully deleted selected records."
                    ],200);
             }else {
                    return response()->json([
                        'status' => false,
                        'message'=> "Something went wrong"
                    ],400);
             }
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' =>'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

  public function deleteFeatures(Request $request){
    try{
        $home_id = $request->home_id;
        $features_id = $request->id;
        $deleteFeatures = TblFeatures::where(['id'=> $features_id, 'home_id'=> $home_id])->delete();

        return response()->json([
            'status' => true,
            'message'=> "Successfully Deleted"
        ],200);

    }catch(\Exception $e) {
        return response()->json([
            'status' => false,
            'message'=> "Internal Error",
            'error' => $e->getMessage()
        ], 500);
    }
  }

  public function deleteImageVideo(Request $request, $id){
    // dd("working......");
    try{
        $data = TblHomeImageVideo::find($id);

        $home_image = "public/home/images/".$data->filename;
        $home_video = "public/home/video/".$data->filename;
        $type = $data->type;
        $home_id = $data->home_id;
        if($type == "image"){
            TblHomeImageVideo::where(['id'=> $id, 'home_id'=> $home_id])->delete();
             Storage::delete($home_image);
                    return response()->json([
                        'status' => true,
                        'message' => 'Successfully Deleted.'
                    ], 200);

        }else if($type == "video"){
            TblHomeImageVideo::where(['id'=> $id, 'home_id'=> $home_id])->delete();
            Storage::delete($home_video);
                   return response()->json([
                       'status' => true,
                       'message' => 'Successfully Deleted.'
                   ], 200);
        }

    }catch(\Exception $e){
        return response()->json([
            'status' => false,
            'message'=> "Internal Error",
            'error' => $e->getMessage()
        ]);
    }
  }

  public function getHomeName(Request $request)
  {
      try {
          $query = TblHome::query();

          if ($request->status) {
              $query->where('status', $request->status);
          }
          if ($request->has('search')) {
              $search = $request->get('search');
              $query->where('home_name', 'like', $search . '%');
          }

          // Select only the 'name' column
         $data = $query->select('id','home_name')->get();


          return response()->json([
              'status' => true,
              'data' => $data,
              'message' => 'Successfully Retrive'
          ], 200);
      } catch (\Exception $e) {
          return response()->json([
              'status' => false,
              'message' => "Internal Error",
              'error' => $e->getMessage(),
          ], 500);
      }
  }

  public function saveHomeAdditionalCharge(Request $request){
    try{
        if(!empty($request->additional_charges)){
            foreach($request->additional_charges as $index=>$objAdditionalCharge){
                $obj = HomeAdditionalCharge::firstOrNew(['id'=>$objAdditionalCharge['id']]);

                $obj->home_id = $request->home_id;
                $obj->name  = $objAdditionalCharge['name'];
                $obj->price  = $objAdditionalCharge['price'];
                $obj->gst     = $objAdditionalCharge['gst'];
                $obj->save();
            }
            return response()->json([
                'status' => true,
                'message' => 'Added Successfully'
            ], 200);
        }
        else {
            return response()->json([
              'status'=>false,
              'message'=> 'Something Went Wrong'
            ], 400);
        }
    }
    catch(\Exception $e){
        return response()->json([
            'status' => false,
            'message' => 'Internal Error',
            'error'=> $e->getMessage()
        ], 500);
    }
  }


  public function deleteHomeAdditionalCharge($id=null){
    try{
        HomeAdditionalCharge::where('id', $id)->delete();
        return response()->json([
            'status' => true,
            'message' => 'Additional Charges Deleted Successfully'
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


  public function saveHomeOwnerDetail(Request $request){
    try{
        $obj = HomeOwnerDetail::firstOrNew(['id'=>$request->id]);
        $obj->home_id = $request->home_id;
        $obj->name = $request->name;
        $obj->email = $request->email;
        $obj->mobile_no = $request->mobile_no;
        $obj->user_name = $request->user_name;
        $obj->password = $request->password;
        $obj->save();
        return response()->json([
            'status' => true,
            'message' => 'Owner Detail Saved Successfully'
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
