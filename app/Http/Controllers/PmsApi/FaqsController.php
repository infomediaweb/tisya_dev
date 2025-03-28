<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblFaq;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FaqsController extends Controller     
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    try {
        
        $query = TblFaq::orderBy('position', 'asc');
        
        // Filter by status if provided
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        // Search by question if search parameter is provided
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('question', 'like', $search . '%');
        }
        
        // Execute the query without pagination
        $data = $query->get();

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


   
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try{
           $validator = Validator::make($request->all(), [
             'question' => 'required',
             'answer' => 'required'
           ]);
           if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Fields are required',
                    'errors' => $validator->errors()
                ], 422);
           } 

           $checkDuplicate = TblFaq::where('question', '=', $request->question)->get();

           $maxPosition = TblFaq::max('position');
           $position = $maxPosition !== null ? $maxPosition + 1 : 0;

           if($checkDuplicate->count() > 0){
                return response()->json([
                    'status' => false,
                    'message' => 'Already added',
                    'errors' => $validator->errors(),
                ], 422);
           }

           $SQL           = new TblFaq();
           $SQL->question = $request->question;
           $SQL->answer   = $request->answer;
           $SQL->position = $position ?? 0;
           $SQL->add_ip   = $request->ip();
           $SQL->add_by   = Auth::user()->name;
           $SQL->save();

           return response()->json([
            'status' => true,
            'message' => 'Added Successfully'
           ]);

        }catch(\Exception $e) {
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
            try{
            $data = TblFaq::findOrFail($id);
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
            }catch(\Exception $e){
                return response()->json([
                    'status' => false,
                    'message' => 'Internal Error',
                    'error' => $e->getMessage()
                    ], 500);
            }
        }

  
    public function update(Request $request, string $id)
    {
        try{

            $UpdateSQL = TblFaq::findOrFail($id);
            $validator = Validator::make($request->all(), [
                'question' => 'required',
                'answer' => 'required'
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'Fields are required',
                    'errors' => $validator->errors()
                ], 422);
            }
             
            $checkDuplicate = TblFaq::where('question', $request->question)
            ->where('id', '!=', $id)
            ->whereNull('deleted_at')
            ->get();

            if ($checkDuplicate->count() > 0) {
                  
                return response()->json([
                    'status' => false,
                    'message' => 'Faqs Already exists',
                    'errors' => 'Faqs Already exists'
                ], 422);

            } else {
               
                $UpdateSQL->question  = $request->question;
                $UpdateSQL->answer      = $request->answer;
                $UpdateSQL->update_ip   = $request->ip();
                $UpdateSQL->update_by   = Auth::user()->name;
                $UpdateSQL->save();
    
                return response()->json([
                    'status' => true,
                    'message' => 'Successfully Updated.'
                ], 200);
    

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
          $data = TblFaq::findOrFail($id);
          if($data){
            $data->delete();
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);

          }else {
            return response()->json([
                'status' => false,
                'message' => 'No record found'
            ], 200);
          }
        }catch(\Exception $e){
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
                TblFaq::whereIn('id', $ids)->delete();

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

    public function updateStatus(Request $request, $id){
        try{
            $data = Tblfaq::findOrFail($id);
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
        }catch(\Exception $e){
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
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
                TblFaq::where('id', $id)->update(['position' => $index]);
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
