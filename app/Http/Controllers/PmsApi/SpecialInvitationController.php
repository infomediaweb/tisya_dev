<?php

namespace App\Http\Controllers\PmsApi;

use Illuminate\Contracts\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblSpecialInvitation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\DiscountCoupon;
use Illuminate\Database\Eloquent\SoftDeletes;


class SpecialInvitationController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            $query = TblSpecialInvitation::with('couponCode')->orderBy('position', 'asc');
            
            // Filtering by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Searching by offer name if search parameter is provided
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where('offer_name', 'like', $search . '%');
            }
            
            // Retrieve all results without pagination
            $specialInvitations = $query->get();
    
            foreach ($specialInvitations as $invitation) {
                $invitation->image_path = $invitation->image ? '/storage/special_invitations/' . $invitation->image : '';
            }
    
            return response()->json([
                'status' => true,
                'data' => $specialInvitations,
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
       
    
    public function store(Request $request){
      
        try {
            // Validate request data
            $validatedData = $request->validate([
                'offer_name' => 'required',
                // Add other validation rules for other fields if needed
            ]);
            // Check for duplicates based on offer name
            $checkDuplicate = TblSpecialInvitation::where('offer_name', $request->offer_name)->first();

            if (!empty($checkDuplicate)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Offer already exists.'
                ], 422);
            }
            else{
                $maxPosition = TblSpecialInvitation::max('position');
                $position = $maxPosition !== null ? $maxPosition + 1 : 0;
        
                // Move image from temporary location to permanent storage
                $tempFile = $request->image;
                $path = 'public/';
                $oldPath =  $path."temp_images/".$tempFile;
                $storingPath = $path."special_invitations/".$tempFile;
                Storage::move($oldPath, $storingPath);
        
                // Create new special invitation record
                $invitation = new TblSpecialInvitation();
                $invitation->offer_name = $validatedData['offer_name'];
                //$invitation->headline = $request->headline;
                $invitation->couponcode_id = $request->couponcode_id;
                $invitation->image = $request->image;
                $invitation->validity = $request->validity;
                $invitation->description = $request->description ?? "";
                $invitation->position = $position ?? 0;
                $invitation->status = $request->status ?? 1;
                $invitation->add_ip = $request->ip();
                $invitation->add_by = Auth::user()->name;
                $invitation->save();
        
                return response()->json([
                    'status' => true,
                    'message' => 'Added successfully'
                ], 201);
            }  
        }
        catch (\Illuminate\Validation\ValidationException $e) {

            // Catch validation errors and return them
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
    
        }
        catch (\Exception $e) {
            // Catch other exceptions and return the custom message
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
        $invitation = TblSpecialInvitation::findOrFail($id);  

        // Add path to the image value
        $invitation->image_path = $invitation->image ? '/storage/special_invitations/' . $invitation->image : '';

        return response()->json([
            'status' => true,
            'data' => $invitation,
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
       
        $duplicateCheck = TblSpecialInvitation::where('offer_name', $request->offer_name)
        ->where('id', '!=', $id)
        ->get();

        if ($duplicateCheck->count() > 0) {
            $duplicateFound = 1;
        } else {
            $duplicateFound = 0;
        }

        if ($duplicateFound == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Offer already exists.'
            ], 422);
        }

        else
        {

        $invitation = TblSpecialInvitation::findOrFail($id);

        // Move image from temporary location to permanent storage
        $tempFile = $request->image;
        $path = 'public/';
        $oldPath =  $path."temp_images/".$tempFile;
        $storingPath = $path."special_invitations/".$tempFile;
        Storage::move($oldPath, $storingPath);

        // Update the special invitation record
        $invitation->offer_name = $request->offer_name;
        //$invitation->headline = $request->headline;
        $invitation->couponcode_id = $request->couponcode_id;
        $invitation->image = $request->image;
        $invitation->validity = $request->validity;
        $invitation->description = $request->description ?? "";
        // $invitation->position = $request->position ?? 0;
        // $invitation->status = $request->status ?? 1;
        $invitation->update_ip = $request->ip();
        $invitation->update_by = Auth::user()->name;
        $invitation->save();

        return response()->json([
            'status' => true,
            'message' => 'Successfully Updated.'
        ], 200);

    }

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Catch validation errors and return them
        return response()->json([
            'status' => false,
            'message' => 'Offer already exists',
        ], 422);

    } catch (\Exception $e) {
        // Catch other exceptions and return the custom message
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
        $invitation = TblSpecialInvitation::findOrFail($id);

        // Delete the special invitation record

        $invitation->status = 0;
        $invitation->save();
        $invitation->delete();

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
            TblSpecialInvitation::whereIn('id', $ids)->delete();

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
        $invitation = TblSpecialInvitation::findOrFail($id);
      
        $request->validate([
            'status' => 'required|boolean',
        ]);
    
        // Update the status field
        $invitation->status = $request->status;
    
        // Save the changes
        if ($invitation->save()) {
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

public function deleteImage($id)
{
    $invitation = TblSpecialInvitation::find($id);
    
    if(!$invitation) {
        return response()->json([
            'status' => false,
            'message' => 'Invitation not found.'
        ], 404);
    }

    $invitationImagePath = "public/special_invitations/" . $invitation->image;
   
  
    if(!empty($invitation->image) && Storage::exists($invitationImagePath)) {
        if(Storage::delete($invitationImagePath)) {
           $invitation->update(['image' => '']);
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
            TblSpecialInvitation::where('id', $id)->update(['position' => $index]);
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

public function getCouponCode() {
    try
    {
         $hoemTypeData = DiscountCoupon::where('status', 1)->get();
         return response()->json([
             'status' => true,
             'data' => $hoemTypeData,
             'message' => 'Sucess'
         ], 200);

     } catch (\Exception $e) {
         return response()->json([
             'status' => 'failed',
             'message' => 'Internal Error'
         ], 500);
     }

 }
   
}
