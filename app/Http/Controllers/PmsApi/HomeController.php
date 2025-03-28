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
use App\Models\TblCollection;
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
use App\Models\HomeOwnerDetail;
use App\Models\TblTag;
use App\Models\TblHomeCollection;
use App\Models\TblHomeTags;
use App\Models\TblReviewImages;
use App\Services\HyperGuestService;
use Carbon\Carbon;

class HomeController extends Controller
{
    
    protected $hyperguest;

    public function __construct(HyperGuestService $hyperguest)
    {
        $this->hyperguest = $hyperguest;
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $query = TblHome::with('homeImageVideo','homeFeatures', 'homecollections', 'homeAmenities', 'homeReviews', 'additionalCharge', 'ownerDetail');
            // $query->with('homeImageVideo','homeFeatures');

            if($request->status){
                $query->where('status', $request->status);
            }
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($query) use ($search) {
                    $query->where('home_name', 'like', $search . '%')
                          ->orWhere('location', 'like', $search . '%')
                          ->orWhere('state', 'like', $search . '%')
                           ->orWhere('internal_name', 'like', $search . '%');
                });
            }
            $perPage = $request->has('take') ? $request->get('take') : 20;

            // convert these data into pagination
            $data = $query->paginate($perPage);

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


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return $request->all();
        try{
            $validator = Validator::make($request->all(), [
               'home_name' => 'required',
               'home_type_id' => 'required',
               'state_id' => 'required',
               'location_id' => 'required',
               'internal_name' => 'required',
               'short_direction' => 'required',
               'no_of_bedrooms' => 'required',
               'guests_included' => 'required',
               'maximum_number_of_guests' => 'required',
               'no_of_bathrooms' => 'required',
               'arrival_time' => 'required',
               'departure_time' => 'required',
               // 'map_latitude' => 'required',
               // 'map_longitude' => 'required',
               'googlelocation_url' => 'required',
               'ru_property_id' => 'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Fields are required',
                    'errors' => $validator->errors()
                ], 422);
            }

            $checkDuplicate = TblHome::where('home_name', '=', $request->home_name)->get();
            if($checkDuplicate->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Already added',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $home_type_id = $request->home_type_id;
            $homeTypeData = TblHomeType::find($home_type_id);
            $home_type = $homeTypeData->name;

            $state_id = $request->state_id;
            $stateData = TblState::find($state_id);
            $state_name = $stateData->name;
            $state_code = $stateData->state_code;

            $locationData = MasterHelper::getLocationList($request->state_id);
            if($locationData->isNotEmpty()){
                $getLocationData = $locationData->where('id', $request->location_id)->first();
                $location_id = $getLocationData->id;
                $location_name = $getLocationData->location_name;
            }


            // if($request->collection_id){
            //     $getCollectionData = TblCollection::where('id', $request->collection_id)->first();
            //     $collection_name = $getCollectionData->collection_name;
            //     $collection_id = $request->collection_id;
            // }else{
            //     $collection_name = "";
            //     $collection_id = 0;
            // }


            $SQL = new TblHome();
            $SQL->home_name  = $request->home_name;
            $SQL->home_type_id = $request->home_type_id;
            $SQL->home_type = $home_type;
            $SQL->state_id = $request->state_id;
            $SQL->state = $state_name;
            $SQL->location_id = $request->location_id;
            $SQL->location = $location_name;
            
           // $SQL->collection_id = $collection_id;
           // $SQL->collection    = $collection_name;
            
            $temp_file = $request->brochure;
            $path = 'public/';
            $oldpath =  $path."temp_images/".$temp_file;  
            $storingPath = $path."brochure/".$temp_file;
            Storage::move($oldpath, $storingPath);
            $SQL->brochure = $temp_file;
            
            $SQL->short_description = $request->short_description;
            $SQL->description = $request->description;
            $SQL->internal_name = $request->internal_name;
            $SQL->location_info = $request->location_info;
            $SQL->short_direction = $request->short_direction;
            $SQL->direction_how_to_get_there = $request->direction_how_to_get_there;
            $SQL->house_rules = $request->house_rules;
            
            $SQL->cancellation_policy = $request->cancellation_policy;
            
            $SQL->hyper_guest_id = $request->hyper_guest_id;
            $SQL->hyper_per_night_price = $request->hyper_per_night_price;
            // $SQL->max_number_of_nights       = $request->max_number_of_nights;
            $SQL->checkin_time  = $request->arrival_time;
            $SQL->checkout_time = $request->departure_time;
            $SQL->maximum_number_of_guests = $request->maximum_number_of_guests	;
            $SQL->guests_included = $request->guests_included;
            $SQL->extra_guest_charges = $request->extra_guest_charges;
            $SQL->no_of_staff = $request->no_of_staff;
            $SQL->no_of_bedrooms = $request->no_of_bedrooms;
            $SQL->no_of_bathrooms  = $request->no_of_bathrooms;
            $SQL->map_latitude = $request->map_latitude;
            $SQL->map_longitude = $request->map_longitude;
            $SQL->googlelocation_url = $request->googlelocation_url;
            if($request->ru_property_id !=''){
                $SQL->ru_property_id  = $request->ru_property_id;
            }
            else{
                $SQL->ru_property_id = rand(1000000, 9999999);
            }
            // $SQL->owner_name                 = $request->owner_name;
            // $SQL->owner_email                = $request->owner_email;
            // $SQL->status                     = $request->status;
            $SQL->add_ip = $request->ip();
            $SQL->add_by = Auth::user()->name;
            $SQL->url_key = Str::of($request->input('home_name'))->slug('-');
            $SQL->slug = Str::of($request->input('home_name'))->slug('-');
            $SQL->meta_title = !empty($request->meta_title) ? $request->meta_title : $request->home_name;
            $SQL->meta_description = !empty($request->meta_description) ? $request->meta_description : $request->home_name;
            $SQL->meta_keyword = !empty($request->meta_keywords) ? $request->meta_keywords : $request->home_name;
            $SQL->save();


            if (isset($request->mappedProperties) && is_array($request->mappedProperties)) {
                //TblHomeCollection::where('home_id', $SQL->id)->delete();
                foreach ($request->mappedProperties as $mapped_property) {
                    TblHomeCollection::create([
                        'home_id' => $SQL->id,
                        'collection_id' => $mapped_property,
                    ]);
                }
            }
            
            
            // hyperguest code
            $hotelCode = $request->hyper_guest_id;
            $start = Carbon::today();
            $end = Carbon::today()->addMonths(6);
            $invTypeCode = 'DBL';
            $ratePlanCode = 'BB';
            $amountAftertax = $request->hyper_per_night_price;
    
            // Availiblity push
            $response = $this->hyperguest->availibityPush(
                $hotelCode,
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
                $invTypeCode,
                $ratePlanCode
            );
            
            // Rate Push
            $response = $this->hyperguest->ratePush(
                $hotelCode,
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
                $invTypeCode,
                $ratePlanCode,
                $amountAftertax
            );
       

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

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        try {
            $data = TblHome::query();
            $type = $request->type ? $request->type : '';
            $data->with('additionalCharge');
            $data->with('ownerDetail');
            $data->with('homecollections');
            
            if($type == 'video'){
                $data->with('homeVideo');
            }
            else if($type == 'image'){
                //$data->with('homeImage');
                $data->with(['homeImage' => function($query) {
                    $query->orderBy('position');
                }]);
                
            }
            else if($type == 'amenities') {
                $data->with('homeAmenities');
            }
            else if($type == 'reviews'){
                $data->with('homeReviews');
            }
            else {
                $data->with('homeFeatures');
            }

            $data = $data->find($id);

            if($data['direction_how_to_get_there'] == null){
                $data['direction_how_to_get_there'] = strval("");
            }
            if($data['short_description'] == null){
                $data['short_description'] = strval("");
            }
            if($data['description'] == null){
                $data['description'] = strval("");
            }
            if($data['house_rules'] == null){
                $data['house_rules'] = strval("");
            }

            if ($data) {
                return response()->json([
                    'status' => true,
                    'data' => $data,
                    'message' => 'Successfully Retrive.'
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Something Went Wrong'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error'=> $e->getMessage()
            ], 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // return $request->all();
        try{
             $SQL = TblHome::findOrFail($id);

             $validator = Validator::make($request->all(), [
                'home_name' => 'required',
                'home_type_id' => 'required',
                'state_id' => 'required',
                'location_id' => 'required',
                'internal_name' => 'required',
                'short_direction' => 'required',
                'no_of_bedrooms' => 'required',
                'guests_included' => 'required',
                'maximum_number_of_guests' => 'required',
                'no_of_bathrooms' => 'required',
                'arrival_time' => 'required',
                'departure_time' => 'required',
                // 'map_latitude' => 'required',
                // 'map_longitude' => 'required',
                'googlelocation_url' => 'required'
             ]);

             if($validator->fails()){
                 return response()->json([
                     'status' => false,
                     'message' => 'Fields are required',
                     'errors' => $validator->errors()
                 ], 422);
             }

             $checkDuplicate = TblHome::where('home_name', '=', $request->home_name)
                                       ->where('id','!=',$id)
                                       ->get();

             if($checkDuplicate->count() > 0) {
                 return response()->json([
                     'status' => false,
                     'message' => 'The name has already been taken',
                     'errors' => $validator->errors(),
                 ], 422);
             }

             $home_type_id = $request->home_type_id;
             $homeTypeData = TblHomeType::find($home_type_id);
             $home_type = $homeTypeData->name;

             $state_id = $request->state_id;
             $stateData = TblState::find($state_id);
             $state_name = $stateData->name;
             $state_code = $stateData->state_code;

             $locationData = MasterHelper::getLocationList($request->state_id);

             if($locationData->isNotEmpty()){
                 $getLocationData = $locationData->where('id', $request->location_id)->first();
                 $location_id = $getLocationData->id;
                 $location_name = $getLocationData->location_name;
             }


            // if($request->collection_id){
            //     $getCollectionData = TblCollection::where('id', $request->collection_id)->first();
            //     $collection_name = $getCollectionData->collection_name;
            //     $collection_id = $request->collection_id;
            // }else{
            //     $collection_name = "";
            //     $collection_id = 0;
            // }
            
            if (isset($request->mappedProperties) && is_array($request->mappedProperties)) {
                TblHomeCollection::where('home_id', $id)->delete();
                foreach ($request->mappedProperties as $mapped_property) {
                    TblHomeCollection::create([
                        'home_id' => $id,
                        'collection_id' => $mapped_property,
                    ]);
                }
            }


            $temp_file = $request->brochure;
            $path = 'public/';
            $oldpath =  $path."temp_images/".$temp_file;  
            $storingPath = $path."brochure/".$temp_file;
            Storage::move($oldpath, $storingPath);
            $SQL->brochure = $temp_file;


             $SQL->home_name                  = $request->home_name;
             $SQL->home_type_id               = $request->home_type_id;
             $SQL->home_type                  = $home_type;
             $SQL->state_id                   = $request->state_id;
             $SQL->state                      = $state_name;
             $SQL->location_id                = $request->location_id;
             $SQL->location                   = $location_name;
             
            // $SQL->collection_id              = $collection_id;
            // $SQL->collection                 = $collection_name;
             
             
             $SQL->hyper_guest_id                = $request->hyper_guest_id;
             $SQL->hyper_per_night_price         = $request->hyper_per_night_price;
             
             $SQL->short_description          = $request->short_description;
             $SQL->description                = $request->description;
             $SQL->location_info              = $request->location_info;
             $SQL->internal_name              = $request->internal_name;
             $SQL->short_direction            = $request->short_direction;
             $SQL->direction_how_to_get_there = $request->direction_how_to_get_there;
             $SQL->house_rules                = $request->house_rules;
             $SQL->cancellation_policy = $request->cancellation_policy;
            //  $SQL->max_number_of_nights       = $request->max_number_of_nights;
             $SQL->checkin_time               = $request->arrival_time;
             $SQL->checkout_time              = $request->departure_time;
             $SQL->maximum_number_of_guests	  = $request->maximum_number_of_guests;
             $SQL->guests_included            = $request->guests_included;
             $SQL->extra_guest_charges        = $request->extra_guest_charges;
             $SQL->no_of_staff                = $request->no_of_staff;
             $SQL->no_of_bedrooms             = $request->no_of_bedrooms;
             $SQL->no_of_bathrooms            = $request->no_of_bathrooms;
             $SQL->map_latitude               = $request->map_latitude;
             $SQL->map_longitude              = $request->map_longitude;
             $SQL->googlelocation_url         = $request->googlelocation_url;
             $SQL->ru_property_id             = $request->ru_property_id;
            //  $SQL->owner_name                 = $request->owner_name;
            //  $SQL->owner_email                = $request->owner_email;
             // $SQL->status                     = $request->status;
             $SQL->update_ip                  = $request->ip();
             $SQL->update_by                  = Auth::user()->name;
             $SQL->url_key                    = Str::of($request->input('home_name'))->slug('-');
             $SQL->meta_title                 = !empty($request->meta_title) ? $request->meta_title : $request->home_name;
             $SQL->meta_description           = !empty($request->meta_description) ? $request->meta_description : $request->home_name;
             $SQL->meta_keyword               = !empty($request->meta_keywords) ? $request->meta_keywords : $request->home_name;
             $SQL->save();
             
            // hyperguest code
            $hotelCode = $request->hyper_guest_id;
            $start = Carbon::today();
            $end = Carbon::today()->addMonths(6);
            $invTypeCode = 'DBL';
            $ratePlanCode = 'BB';
            $amountAftertax = $request->hyper_per_night_price;
    
            // Availiblity push
            $response = $this->hyperguest->availibityPush(
                $hotelCode,
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
                $invTypeCode,
                $ratePlanCode
            );
            
            // Rate Push
            $response = $this->hyperguest->ratePush(
                $hotelCode,
                $start->format('Y-m-d'),
                $end->format('Y-m-d'),
                $invTypeCode,
                $ratePlanCode,
                $amountAftertax
            );

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


    public function updateStatus(Request $request, $id){

        try{
            $query = TblHome::findOrFail($id);
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
    public function destroy(string $id)
    {
        try{
            $query = TblHome::findOrFail($id);
            if ($query) {
                $query->homeImageVideo()->delete();
                $query->homeFeatures()->delete();
                $query->homeAmenities()->delete();
                $query->homeReviews()->delete();
                $query->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Deleted.'
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

    public function deleteMultipleRecord(Request $request)
    {
        try{
           $ids = $request->get('ids');
            if(!empty($ids)){
                TblHome::with('homeImageVideo','homeFeatures', 'homeAmenities', 'homeReviews')
                       ->whereIn('id', $ids)
                       ->get()
                       ->each(function ($homeData) {
                            $homeData->homeImageVideo()->delete();
                            $homeData->homeFeatures()->delete();
                            $homeData->homeAmenities()->delete();
                            $homeData->homeReviews()->delete();
                            $homeData->delete();
                       });
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

        }catch(\Exception $e)
        {
            return response()->json([
                'status' => false,
                'message'=> 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    
    // public function saveGallery(Request $request){
    //         try{
    //         if(!empty($request->filename)){
    //             $home_id = $request->home_id;
    //             $type = $request->type;
    //             $deleteimgsql = TblHomeImageVideo::where(['home_id'=> $home_id, 'type'=> $type])->delete();
    //             foreach($request->filename as $index => $objImg){
    //                 if(!str_contains($objImg['filename'], 'storage/home/images')){
    //                     $title = isset($objImg['title']) ? $objImg['title'] : null;
    //                     $defaultImage = isset($objImg['default']) ? (int)$objImg['default'] : 0;
    //                     $temp_file = $objImg['filename'];
    //                     $newFileName = (strpos($temp_file, $home_id."-home-") !== false) ? $temp_file : $home_id . "-home-" . $temp_file;
    //                     $temp_filepath = $objImg['filepath'];
    //                     $path = 'public/';
    //                     $oldpath =  $path."temp_images/".$temp_file;  
    //                     $storingPath = $path."home/images/".$newFileName; 
    //                     Storage::move($oldpath, $storingPath);
    //                     $Image_SQL               = new TblHomeImageVideo();
    //                     $Image_SQL->home_id      = $home_id;
    //                     $Image_SQL->type         = $type;
    //                     $Image_SQL->title        = $title;
    //                     $Image_SQL->filename     = $newFileName;
    //                     $Image_SQL->default      = $defaultImage;
    //                     $Image_SQL->position     = $index;
    //                     $Image_SQL->add_ip       = $request->ip();
    //                     $Image_SQL->add_by       = Auth::user()->name;
    //                     $Image_SQL->save();
    //                 }
    //                 else{
    //                     $explode = explode('/', $objImg['filename']);
    //                     $detail = TblHomeImageVideo::firstOrNew(['filename'=>$explode[3]]);
    //                     $detail->title = $objImg['title'];
    //                     $detail->home_id  = $home_id;
    //                     $detail->add_ip       = $request->ip();
    //                     $detail->add_by       = Auth::user()->name;
    //                     $detail->position = $index;
    //                     $detail->save();
    //                 }
    //             }
    //             return response([
    //                 'status' => true,
    //                 'message' => 'Added Successfully'
    //             ], 200);
    //         }else {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Something Went Wrong'
    //             ], 400);
    //         }
    //       }catch(\Exception $e){
    //             return response([
    //                 'status' => false,
    //                 'message' => 'Error!, please try again later.',
    //                 'error' => $e->getMessage()
    //             ], 400);
    //       }
    //     }



public function saveGallery(Request $request)
        {
            try {
                if (!empty($request->filename)) {
                    $home_id = $request->home_id;
                    $type = $request->type;
                    foreach ($request->filename as $index => $objImg) {
                        if (!str_contains($objImg['filename'], 'storage/home/images')) {
                            $title = isset($objImg['title']) ? $objImg['title'] : null;
                            $defaultImage = isset($objImg['default']) ? (int)$objImg['default'] : 0;
                            $temp_file = $objImg['filename'];
                            $newFileName = (strpos($temp_file, $home_id . "-home-") !== false) ? $temp_file : $home_id . "-home-" . $temp_file;
                            $path = 'public/';
                            $oldpath = $path . "temp_images/" . $temp_file;  
                            $newDirectory = 'public/home/small/';  
                            $newmediumDirectory = 'public/home/medium/';  
                            $storingPathImages = $path . "home/images/" . $newFileName;  
                            $this->resizeAndOptimizeImage($oldpath, $storingPathImages);
                            $this->resizeAndOptimizeImage($oldpath, $newDirectory . $newFileName);
                            $this->resizeAndOptimizeImageMedium($oldpath, $newmediumDirectory . $newFileName);
                            Storage::move($oldpath, $storingPathImages);
                            // Store the image details in the database
                            $Image_SQL = new TblHomeImageVideo();
                            $Image_SQL->home_id = $home_id;
                            $Image_SQL->type = $type;
                            $Image_SQL->title = $title;
                            $Image_SQL->image_slug = Str::of($title)->slug('-');
                            $Image_SQL->filename = $newFileName;
                            $Image_SQL->default = $defaultImage;
                            $Image_SQL->position = $index;
                            $Image_SQL->add_ip = $request->ip();
                            $Image_SQL->add_by = Auth::user()->name;
                            $Image_SQL->save();
                        } else {
                            $explode = explode('/', $objImg['filename']);
                            $detail = TblHomeImageVideo::firstOrNew(['filename' => $explode[3]]);
                            $detail->title = $objImg['title'];
                            $detail->image_slug = Str::of($objImg['title'])->slug('-');
                            $detail->position = $index;
                            $detail->save();
                        }
                    }
        
                    return response([
                        'status' => true,
                        'message' => 'Added Successfully'
                    ], 200);
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => 'Something Went Wrong'
                    ], 400);
                }
            } catch (\Exception $e) {
                return response([
                    'status' => false,
                    'message' => 'Error! Please try again later.',
                    'error' => $e->getMessage()
                ], 400);
            }
        }
// Helper method to resize and optimize the image
private function resizeAndOptimizeImage($oldpath, $storingPath)
{
    $imageContent = Storage::get($oldpath);
    $image = imagecreatefromstring($imageContent);
    if (!$image) {
        throw new \Exception('Failed to load image');
    }
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);
    $resizeWidth = 600; 
    $resizeHeight = ($resizeWidth / $imageWidth) * $imageHeight; 
    $resizedImage = imagecreatetruecolor($resizeWidth, $resizeHeight);
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $imageWidth, $imageHeight);
    $dir = dirname($storingPath);
    if (!Storage::exists($dir)) {
        Storage::makeDirectory($dir, 0777, true); 
    }
    imagejpeg($resizedImage, Storage::path($storingPath), 85);  
    imagedestroy($image);
    imagedestroy($resizedImage);
}

private function resizeAndOptimizeImageMedium($oldpath, $storingPath)
{
    $imageContent = Storage::get($oldpath);
    $image = imagecreatefromstring($imageContent);
    if (!$image) {
        throw new \Exception('Failed to load image');
    }
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);
    $resizeWidth = 1000; 
    $resizeHeight = ($resizeWidth / $imageWidth) * $imageHeight; 
    $resizedImage = imagecreatetruecolor($resizeWidth, $resizeHeight);
    imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $resizeWidth, $resizeHeight, $imageWidth, $imageHeight);
    $dir = dirname($storingPath);
    if (!Storage::exists($dir)) {
        Storage::makeDirectory($dir, 0777, true); 
    }
    imagejpeg($resizedImage, Storage::path($storingPath), 85);  
    imagedestroy($image);
    imagedestroy($resizedImage);
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
             $allData = MasterHelper::getAmenities();
            $homeAmenities = TblHomeAmenities::where('home_id', $id)
                ->get(['amenities_id', 'position']) 
                ->keyBy('amenities_id') 
                ->toArray();
            foreach ($allData as &$amenity) {
                $amenityId = is_array($amenity) ? $amenity['id'] : $amenity->id;
                if (isset($homeAmenities[$amenityId])) {
                    $amenity['isChecked'] = true;
                    $amenity['position'] = $homeAmenities[$amenityId]['position'];
                } else {
                    $amenity['isChecked'] = false;
                    $amenity['position'] = PHP_INT_MAX; 
                }
            }
            usort($allData, function ($a, $b) {
                return $a['position'] <=> $b['position'];
            });
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
        //dd($request);
        try{
           $SQLReview = TblHomeReview::firstOrNew(['id'=>$id]);
           $validator = validator::make($request->all(), [
               'guest_name' => 'required',
               'review_date' => 'required',
               'rating' => 'required',
               'comment' => 'required',
               'review_type' => 'required',
               //'icons_image' => 'required',
           ]);
           if($validator->fails()){
             return response()->json([
                'status' => false,
                'message' => 'Fields are required',
                'error' => $validator->errors()
             ], 422);
           }
           
           $temp_file = $request->icons_image; 
           if ($temp_file) {
               $tempPath    = "public/temp_images/" . $temp_file; 
               $finalPath   = "public/review/images/" . $temp_file; 
               if (Storage::exists($tempPath)) {
                   Storage::move($tempPath, $finalPath);
                   $SQLReview->icons_image = $temp_file; 
               }
               $newPath = asset('storage/review/images/' . $temp_file);
           }
          
        //    $SQLReview              = new TblHomeReview();
           $SQLReview->home_id     = $request->home_id;
           $SQLReview->guest_name  = $request->guest_name;
           $SQLReview->review_date = $request->review_date;
           $SQLReview->rating      = (float) $request->rating;
           $SQLReview->comment     = $request->comment;
         //  $SQLReview->icons_image     = $temp_file;
           $SQLReview->review_type     = $request->review_type;
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
           // $data = TblHomeReview::find($id);
            $data = TblHomeReview::select('tbl_home_reviews.*', 'icons_image as icons_image_name', DB::raw('CONCAT("/storage/review/images/",icons_image) as icons_image'))->find($id);
            
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
                $obj->type_option  = $objAdditionalCharge['type_option'];
                $obj->display_on_website = $objAdditionalCharge['display_on_website'];
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
  
  public function saveTags(Request $request){
    try{
        $home_id = $request->home_id;
        $amenities = $request->amenities;
        TblHomeTags::where('home_id', $home_id)->delete();

        foreach($amenities as $key => $objAmenities){
           $amenities_id = $objAmenities['tags_id'];
           $amenities_name = $objAmenities['tags_name'];
           $amenities_number = 0;

           $SQL                   = new TblHomeTags();
           $SQL->home_id          = $home_id;
           $SQL->tags_id     = $amenities_id;
           $SQL->tags_name   = $home_id;
           $SQL->tags_number = $amenities_number;
           $SQL->position         = $key;
          // $SQL->add_ip           = $request->ip();
         //  $SQL->add_by           = Auth::user()->name;
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

 public function showAllTags(Request $request, string $id) {
    try{
        
        /* $allData = MasterHelper::getTags(); 
        $homeAmenities = TblHomeTags::where('home_id', $id)->pluck('tags_id')->toArray();
        foreach ($allData as &$amenity) { 
            $amenityId = is_array($amenity) ? $amenity['id'] : $amenity->id;
            $amenity['isChecked'] = in_array($amenityId, $homeAmenities) ? true : false;
        } */
       // $allData = MasterHelper::getTags(); 
       $allData = TblTag::select('tbl_tags.*', DB::raw('CONCAT("/storage/home/images/",tags_image) as tags_image'),)->where('status', 1)->get()->toArray();
        $homeAmenities = TblHomeTags::where('home_id', $id)
            ->get(['tags_id', 'position']) 
            ->keyBy('tags_id') 
            ->toArray();
        foreach ($allData as &$amenity) {
            $amenityId = is_array($amenity) ? $amenity['id'] : $amenity->id;
            if (isset($homeAmenities[$amenityId])) {
                $amenity['isChecked'] = true;
                $amenity['position'] = $homeAmenities[$amenityId]['position'];
            } else {
                $amenity['isChecked'] = false;
                $amenity['position'] = PHP_INT_MAX; 
            }
        }
        usort($allData, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

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
public function updateShowOnHome(Request $request, $id){

    try{
        $query = TblHome::findOrFail($id);
        $request->validate([
            'show_on_home'=> 'required|boolean',
        ]);

        $query->show_on_home = $request->show_on_home;
        if ($query->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Show on home page updated successfully'
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

public function updateShowOnApartment(Request $request, $id){

    try{
        $query = TblHome::findOrFail($id);
        $request->validate([
            'show_on_apartment'=> 'required|boolean',
        ]);

        $query->show_on_apartment = $request->show_on_apartment;
        if ($query->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Show on apartment page updated successfully'
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

public function deleteImagebrochure($id){
    
    $locations = TblHome::find($id);
    $pdf = "public/brochure/".$locations->brochure;
    if(!empty($pdf)){
        if(Storage::delete($pdf)){
            $locations->update(['brochure' => null]);
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);

        }

    }

}
public function deleteImage($id){
    $icons = TblHomeReview::find($id);
    $icons_image = "public/review/images/".$icons->icons_image;
    if(!empty($icons_image)){
        if(Storage::delete($icons_image)){
            $icons->update(['icons_image' => null]);
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);
        }
    }
}

public function updateOnlyForEnquiry(Request $request, $id){

    try{
        $query = TblHome::findOrFail($id);
        $request->validate([
            'only_for_enquiry'=> 'required|boolean',
        ]);

        $query->only_for_enquiry = $request->only_for_enquiry;
        if ($query->save()) {
            return response()->json([
                'status' => true,
                'message' => 'enquiry page updated successfully'
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
public function getReviewImage() {
    try {
        
         $homeTypeData = TblReviewImages::get();
        return response()->json([
            'status' => true,
            'data' => $homeTypeData,
            'message' => 'Success'
        ], 200);

    } catch (\Exception $e) {
        // Return error response if exception occurs
        return response()->json([
            'status' => false,  // changed to 'false' for consistency
            'message' => 'Internal Error'
        ], 500);
    }
}



}
