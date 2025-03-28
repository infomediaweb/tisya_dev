<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblAmenities;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
// use DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AmenitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //

        try {

            $query = TblAmenities::select('tbl_amenities.*', DB::raw('CONCAT("/storage/amenities/",amenities_image) as amenities_image'),);
            // return $query->get();
            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('amenities_name', 'like', $search . '%');
            }
            // Take the counting of per page data
            $perPage = $request->has('take') ? $request->get('take') : 20;

            // convert these data into pagination
            $data = $query->paginate($perPage);

            // return $data->items();
            // return $data->url($page);
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        try {
            // return $request->all();
            $validator = Validator::make($request->all(), [
                'amenities_name' => 'required'
            ]);


            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ],
                    422
                );
            }


            $checkDuplicate = TblAmenities::where('amenities_name', '=', $request->amenities_name)
                ->get();

            if ($checkDuplicate->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Already added',
                    'errors' => $validator->errors(),
                ], 422);
            }

            /****start the proccess of move the file into permanent folder */

            // change this with requested file name

            $temp_file = $request->amenities_image; //'storage/temp_images/65d09ae3648bd_search.png' -- 
            
            $temp_filepath = $request->amenities_image_filepath; //'storage/temp_images/65d09ae3648bd_search.png'

            

            $path = 'public/';
            
            $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
            $storingPath = $path."amenities/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);

            
            Storage::move($oldpath, $storingPath); // move the file to newpath
            // Storage::copy($oldpath, $newPath);
            
            //get the new filename for store into database and read the file via url
            $newPath = 'storage/amenities/'.$temp_file; //str_replace('public/', 'storage/', $storingPath);


            $SQL = new TblAmenities();
            $SQL->amenities_name = $request->amenities_name;
            $SQL->amenities_image =  $temp_file;
            //$SQL->status           = $request->status;
            $SQL->add_ip           = $request->ip();
            $SQL->add_by           = Auth::user()->name;

            $SQL->save();

            return response([
                'status' => true,
                'message' => 'Added Successfully'
            ], 200);
        } catch (\Exception $e) {

            //dd($e);
            return response([
                'status' => false,
                'message' => 'Error!, please try again later.'
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
    public function show(Request $request, $id)
    {
        //
        try {
            
            $data = TblAmenities::select('tbl_amenities.*', 'amenities_image as amenities_image_name', DB::raw('CONCAT("/storage/amenities/",amenities_image) as amenities_image'))->find($id);
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
                'message' => 'Internal Error'
            ], 500);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // return $request->all();
        try {


            $validator = Validator::make($request->all(), [
                'amenities_name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ],
                    422
                );
            }

            $checkDuplicate = TblAmenities::where('amenities_name', '=', $request->amenities_name)
                ->where('id', '!=', $id)
                ->get();

            if ($checkDuplicate->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'The name has already been taken',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $temp_file = $request->amenities_image; //'storage/temp_images/65d09ae3648bd_search.png' -- 
            
            $temp_filepath = $request->amenities_image_filepath; //'storage/temp_images/65d09ae3648bd_search.png'

            

            $path = 'public/';
            
            $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
            $storingPath = $path."amenities/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);

            
            Storage::move($oldpath, $storingPath); // move the file to newpath
            // Storage::copy($oldpath, $newPath);
            
            //get the new filename for store into database and read the file via url
            $newPath = 'storage/amenities/'.$temp_file;

            $SQL = TblAmenities::findOrFail($id);

            $SQL->amenities_name = $request->amenities_name;
            $SQL->amenities_image =  $temp_file;
            $SQL->update_ip           = $request->ip();
            $SQL->update_by           = Auth::user()->name;

            $SQL->save();

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



    public function updateStatus(Request $request, $id)
    {
        try {
            $data = TblAmenities::findOrFail($id);
            $request->validate([
                'status' => 'required|boolean',
            ]);
            $data->status = $request->status;

            if ($data->save()) {
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
        //
        try {
            $data = TblAmenities::findOrFail($id);
            if ($data) {
                $data->delete();
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
        //

        try {
            $ids = $request->get('ids');

            if (!empty($ids)) {
                TblAmenities::whereIn('id', $ids)->delete();

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

    public function deleteImage($id){
        $amenities = TblAmenities::find($id);
        
        $amenities_image = "public/amenities/".$amenities->amenities_image;
        if(!empty($amenities_image)){
            if(Storage::delete($amenities_image)){
                $amenities->update(['amenities_image' => null]);
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Deleted.'
                ], 200);
            }
        }
    }
    
    public function updateShowOnFilter(Request $request, $id){

        try{
            $query = TblAmenities::findOrFail($id);
            $request->validate([
                'show_on_filter'=> 'required|boolean',
            ]);

            $query->show_on_filter = $request->show_on_filter;
            if ($query->save()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Show on filter page updated successfully'
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
}
