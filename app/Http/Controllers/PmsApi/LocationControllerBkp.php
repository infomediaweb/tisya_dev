<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblLocation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use App\Models\RuLocation;

use DB;

class LocationController extends Controller{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
    
        
        try {
            $query = TblLocation::query();

            $query->leftJoin('tbl_states','tbl_states.id','=','tbl_location.state_id');
            $query->select('tbl_location.*','name as state_name', 'state_code');

            if ($request->has('status')) {
                $query->where('tbl_location.status', $request->status);
            } else {
                // Include records with status 0 or 1
                $query->whereIn('tbl_location.status', [0, 1]);
            }

            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('location_name', 'like', $search . '%');
            }
            // Take the counting of per page data
            $perPage = $request->has('take') ? $request->get('take') : 5;

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



//     public function index(Request $request)
// {
//     try {
//         $query = TblLocation::query();

//         $query->leftJoin('tbl_states', 'tbl_states.id', '=', 'tbl_location.state_id');
//         $query->select('tbl_location.*', 'name as state_name', 'state_code');

//         // Filter by status equal to 1
//         $query->where('tbl_location.status', 1);

//         if ($request->has('search')) {
//             $search = $request->get('search');
//             $query->where('location_name', 'like', $search . '%');
//         }

//         // Take the count of per page data
//         $perPage = $request->has('take') ? $request->get('take') : 5;

//         // Convert the data into pagination
//         $data = $query->paginate($perPage);

//         return response()->json([
//             'status' => true,
//             'data' => $data,
//             'message' => 'Successfully Retrieved.'
//         ], 200);
//     } catch (\Exception $e) {
//         return response()->json([
//             'status' => false,
//             'message' => 'Internal Error',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }


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
                'location_name' => 'required',
                'state_id' => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ], 422);
            }

            $checkDuplicate = TblLocation::where('state_id','=', $request->state_id)
                              ->where('location_name', '=', $request->location_name)
                              ->get();

            if($checkDuplicate->count() > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'The location name has already been taken',
                    'errors' => $validator->errors(),
                ], 422);
            }
            // else {
            //     dd("notfound");
            // }
            $loc = new TblLocation();
            $loc->state_id = $request->state_id;
            $loc->location_name = $request->location_name;
            $loc->slug_name        = Str::of($request->input('location_name'))->slug('-');
            $loc->status           = $request->status;
            $loc->meta_title       = !empty($request->meta_title) ? $request->meta_title : $request->location_name;
            $loc->meta_description = !empty($request->meta_description) ? $request->meta_description : $request->location_name;
            $loc->meta_keyword    = !empty($request->meta_keywords) ? $request->meta_keywords : $request->location_name;


             $loc->save();

            return response([
                'status' => true,
                'message' => 'Added Successfully'
            ], 200);

        } catch (\Exception $e) {

            // dd($e);
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
            $data = TblLocation::find($id);
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

            $data = TblLocation::findOrFail($id);


            /*
            $validator = Validator::make($request->all(), [
                'location_name' => [
                    'required',
                    Rule::unique('tbl_location', 'location_name')
                        ->where(function ($query) {
                            $query->whereNull('deleted_at');
                        })
                        ->ignore($data->id, 'id'), // Adjust 'id' if needed
                ],
                 'state_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'The name has already been taken',
                    'errors' => $validator->errors(),
                ], 422);
            }
            */

            $validator = Validator::make($request->all(), [
                'location_name' => 'required',
                'state_id' => 'required',
            ]);


            if ($validator->fails()) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'Fields are required',
                        'errors' => $validator->errors(),
                    ], 422);
            }

            $checkDuplicate = TblLocation::where('state_id','=', $request->state_id)
                              ->where('location_name', '=', $request->location_name)
                              ->where('id','!=',$id)
                              ->get();

            if($checkDuplicate->count() > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'The location name has already been taken',
                    'errors' => $validator->errors(),
                ], 422);
            }


            $data->state_id = $request->state_id;
            $data->location_name = $request->location_name;
            $data->slug_name        = Str::of($request->input('location_name'))->slug('-');
            $data->status           = $request->status;
            $data->meta_title       = !empty($request->meta_title) ? $request->meta_title : $request->location_name;
            $data->meta_description = !empty($request->meta_description) ? $request->meta_description : $request->location_name;
            $data->meta_keyword    = !empty($request->meta_keywords) ? $request->meta_keywords : $request->location_name;
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
             $data = TblLocation::findOrFail($id);
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
            $data = TblLocation::findOrFail($id);
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
                TblLocation::whereIn('id', $ids)->delete();

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





    public function getLocations(Request $request,$state_id){
       try{
            //$state_id= $request->state_id;

            $data = MasterHelper::getLocationList($state_id);

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


}
