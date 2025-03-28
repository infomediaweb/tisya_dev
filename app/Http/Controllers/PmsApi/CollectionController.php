<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblLocation;
use App\Models\TblCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use App\Models\RuLocation;
use App\Models\PropertyBooking;
use Illuminate\Support\Facades\Storage;
use DB;

class CollectionController extends Controller{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){


        try {
            
            $query = TblCollection::query()
            ->select(
                'tbl_collection.*',
                DB::raw('CONCAT("/storage/collection/images/", image) as image'),
            );
            
            if ($request->has('status')) {
                $query->where('tbl_collection.status', $request->status);
            }
            if ($request->has('existing_property_id')) {
                if($request->existing_property_id !=''){
                    $propertyBooking = PropertyBooking::where('id', $request->existing_property_id)->first();
                    $query->where('tbl_collection.id', $propertyBooking->location_id);
                }
            }
            else {
                $query->whereIn('tbl_collection.status', [0, 1]);
            }

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('collection_name', 'like', $search . '%');
            }
            // Take the counting of per page data
            $perPage = $request->has('take') ? $request->get('take') : 15;

            // convert these data into pagination
            $data = $query->paginate($perPage);

            // return $data->items();
            // return $data->url($page);
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Successfully Retrive.'
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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // return $request->all();
            $validator = Validator::make($request->all(), [
                'collection_name' => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ], 422);
            }

            $checkDuplicate = TblCollection::where('collection_name', '=', $request->collection_name)->get();

            if($checkDuplicate->count() > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'The collection name has already been taken',
                    'errors' => $validator->errors(),
                ], 422);
            }
            
            $temp_file = $request->image;
            $path = 'public/';
            $oldpath =  $path."temp_images/".$temp_file;  
            $storingPath = $path."collection/images/".$temp_file;
            Storage::move($oldpath, $storingPath);
            $loc = new TblCollection();
            $loc->collection_name = $request->collection_name;
            $loc->collection_description = $request->collection_description;
            $loc->slug_name        = Str::of($request->input('collection_name'))->slug('-');
            $loc->status           = $request->status;
            $loc->image = $temp_file;
            $loc->save();
            return response([
                'status' => true,
                'message' => 'Added Successfully'
            ], 200);

        } catch (\Exception $e) {
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
            $data = TblCollection::select('tbl_collection.*', 'image as image_name', DB::raw('CONCAT("/storage/collection/images/",image) as image'))->find($id);
            //$data = TblCollection::find($id);
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
        //
        try {

            $data = TblCollection::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'collection_name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ], 422);
            }
            $checkDuplicate = TblCollection::where('collection_name', '=', $request->collection_name)
                              ->where('id','!=',$id)
                              ->get();
            if($checkDuplicate->count() > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'The collection name has already been taken',
                    'errors' => $validator->errors(),
                ], 422);
            }
            $temp_file = $request->image;
            $path = 'public/';
            $oldpath =  $path."temp_images/".$temp_file;  
            $storingPath = $path."collection/images/".$temp_file;
            Storage::move($oldpath, $storingPath);
            $data->image = $temp_file;
            $data->collection_name = $request->collection_name;
            $data->collection_description = $request->collection_description;
            $data->slug_name        = Str::of($request->input('collection_name'))->slug('-');
            $data->status           = $request->status;
            $data->save();
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


    /**
     * Update status the specified resource in storage.
     */

     public function updateStatus(Request $request, $id)
     {
         try {
             $data = TblCollection::findOrFail($id);
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
            $data = TblCollection::findOrFail($id);
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

    public function deleteMultipleRecord(Request $request){
        //
        try {
            $ids = $request->get('ids');

            if (!empty($ids)) {
                TblCollection::whereIn('id', $ids)->delete();
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





    public function getCollection(Request $request){
       try{
            $data = TblCollection::get();
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Sucess'
            ], 200);

        }
        catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Internal Error'
            ], 500);
        }

    }

    public function getRuLocationList(){

        try {
            $xml = "<Pull_ListPropertyAvailabilityCalendar_RQ>
                <Authentication>
                    <UserName>".env('RU_USER_NAME')."</UserName>
                    <Password>".env('RU_PASSWORD')."</Password>
                </Authentication>
                <PropertyID>1</PropertyID>

                <DateFrom>2024-04-06</DateFrom>
                <DateTo>2024-04-16</DateTo>
            </Pull_ListPropertyAvailabilityCalendar_RQ>";

            $ruLocationList = MasterHelper::makeXmlRequest($xml);

            return response()->json([
                'status' => true,
                'message' => 'Successfully retrieved.'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function allLocation(Request $request){
        try {
            $query = TblLocation::query();
            $query->leftJoin('tbl_states','tbl_states.id','=','tbl_location.state_id');
            $query->select('tbl_location.*','name as state_name', 'state_code');
            if ($request->has('status')) {
                $query->where('tbl_location.status', $request->status);
            }
            else {
                // Include records with status 0 or 1
                $query->whereIn('tbl_location.status', [0, 1]);
            }
            if ($request->has('existing_property_id')) {
                if($request->existing_property_id !=''){
                    $propertyBooking = PropertyBooking::where('id', $request->existing_property_id)->first();
                    $query->where('tbl_location.id', $propertyBooking->location_id);
                }
            }
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('location_name', 'like', $search . '%');
            }
            // Take the counting of per page data
            $perPage = $request->has('take') ? $request->get('take') : 100;
            // convert these data into pagination
            $data = $query->paginate($perPage);
            // return $data->items();
            // return $data->url($page);
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Successfully Retrive.'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteImageLocation($id){
    
        $locations = TblCollection::find($id);
        $image = "public/collection/".$locations->image;
        if(!empty($image)){
            if(Storage::delete($image)){
                $locations->update(['image' => null]);
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Deleted.'
                ], 200);

            }

        }

    }

    public function updateShowOnCollection(Request $request, $id){

        try{
            $query = TblCollection::findOrFail($id);
            $request->validate([
                'show_on_collection'=> 'required|boolean',
            ]);

            $query->show_on_collection_page = $request->show_on_collection;
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


}
