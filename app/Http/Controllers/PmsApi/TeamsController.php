<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblTeam;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class TeamsController extends Controller
{
    
    public function index(Request $request)
{
    try {
         $query = TblTeam::orderBy('position', 'asc');
        
        // Filtering by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Searching by name if search parameter is provided
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', $search . '%');
        }
        
        // Retrieve all results without pagination
        $data = $query->get();
        

        foreach ($data as $company) {
            $company->image_path = $company->image ? '/storage/teams/' . $company->image : '';
            // $company->image_path = '/storage/teams/' . $company->image;
        }

        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'Successfully retrieved.'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => "Internal Error",
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function store(Request $request)
    {
        try {
            // Validate request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:200',
                'designation' => 'required|string|max:200',
                'image' => 'required|string|max:200',
                'description' => 'required|string',
                'position' => 'integer',
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
    
            // Check for duplicates based on name (if needed)
            $checkDuplicate = TblTeam::where('name', '=', $request->name)->first();
            
            $maxPosition = TblTeam::max('position');
            $position = $maxPosition !== null ? $maxPosition + 1 : 0;

            if (!is_null($checkDuplicate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Team member name has already been taken'
                ], 422);
            }

            $temp_file = $request->image; //'storage/temp_images/65d09ae3648bd_search.png' -- 
            
            // $temp_filepath = $request->amenities_image_filepath; //'storage/temp_images/65d09ae3648bd_search.png'

            $path = 'public/';
            
            $oldpath =  $path."temp_images/".$temp_file;  //'public/temp_images/65d09ae3648bd_search.png'
            $storingPath = $path."teams/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);

            Storage::move($oldpath, $storingPath);



    
            // Create new team record
            $team = new TblTeam();
            $team->name = $request->name;
            $team->designation = $request->designation;
            $team->image = $request->image;
            $team->description = $request->description;
            $team->position = $position ?? 0;
            $team->status = $request->status ?? 1;
            $team->add_ip = $request->ip();
            $team->add_by = Auth::user()->name;
            $team->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Team Member added successfully'
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
        $data = TblTeam::findOrFail($id);  

        // Add path to the image value
        $data->image_path = $data->image ? '/storage/teams/' . $data->image : '';
        // $data->image_path = '/storage/teams/' . $data->image;

        return response()->json([
            'status' => true,
            'data' => $data,
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
                $updateSQL = TblTeam::findOrFail($id);

                // Validate request data
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:200',
                    'designation' => 'required|string|max:200',
                    'image' => 'required|string|max:200',
                    'description' => 'required|string',
                    'position' => 'integer',
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
                $storingPath = $path."teams/".$temp_file; //str_replace('temp_images/', 'amenities/', $oldpath);
    
                Storage::move($oldpath, $storingPath);

                // Update the team record
                $updateSQL->name = $request->name;
                $updateSQL->designation = $request->designation;
                $updateSQL->image = $request->image;
                $updateSQL->description = $request->description;
                
                    
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
                    $team = TblTeam::findOrFail($id);

                    // Delete the team record
                    $team->delete();

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
                        TblTeam::whereIn('id', $ids)->delete();

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
                    $team = TblTeam::findOrFail($id);
                  
                    $request->validate([
                        'status' => 'required|boolean',
                    ]);
            
                    // Update the status field
                    $team->status = $request->status;
            
                    // Save the changes
                    if ($team->save()) {
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
             
           
           public function deleteImage($id) {
            $team = Tblteam::find($id);
            
            if(!$team) {
                return response()->json([
                    'status' => false,
                    'message' => 'Team not found.'
                ], 404);
            }

            $team_image_path = "public/teams/" . $team->image;
       
      
            if(!empty($team->image) && Storage::exists($team_image_path)) {
                if(Storage::delete($team_image_path)) {
                   $team->update(['image' => '']);
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


          public function savePosition(Request $request)
          {
      
              try {
                  $positions = $request->position;
                    
                  
                  // Check if $positions is an array
                  if (!is_array($positions)) {
                      throw new \Exception('$positions must be an array.');
                  }
          
                  foreach ($positions as $index => $id) {
                    Tblteam::where('id', $id)->update(['position' => $index]);
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
