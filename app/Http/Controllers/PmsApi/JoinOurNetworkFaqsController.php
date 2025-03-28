<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TblJoinOurNetworkFaqs;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JoinOurNetworkFaqsController extends Controller
{
 
    public function index(Request $request)
    {
        try {
            
            $query = TblJoinOurNetworkFaqs::orderBy('position', 'asc');
            
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
    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'question' => 'required',
                'answer' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Fields are required',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $checkDuplicate = TblJoinOurNetworkFaqs::where('question', '=', $request->question)->get();
    
            if ($checkDuplicate->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Question already exists',
                ], 422);
            }
    
            $maxPosition = TblJoinOurNetworkFaqs::max('position');
            $position = $maxPosition !== null ? $maxPosition + 1 : 0;
    
            $faq = new TblJoinOurNetworkFaqs();
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            $faq->position = $position;
            $faq->add_ip = $request->ip();
            $faq->add_by = Auth::user()->name;
            $faq->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Added Successfully'
            ]);
    
        } catch (\Exception $e) {
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
            $faq = TblJoinOurNetworkFaqs::findOrFail($id);
    
            return response()->json([
                'status' => true,
                'data' => $faq,
                'message' => 'Successfully retrieved.'
            ], 200);
    
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Record not found.'
                ], 404);
            }
    
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
             
            $checkDuplicate = TblJoinOurNetworkFaqs::where('question', '=', $request->question)
                                       ->whereNull('deleted_at')
                                       ->where('id','!=',$id)
                                       ->get();

    
            if ($checkDuplicate->count() > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Question already exists',
                ], 422);
            }

            else
            {

            $faq = TblJoinOurNetworkFaqs::findOrFail($id);
    
            $validator = Validator::make($request->all(), [
                'question' => 'required',
                'answer' => 'required'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Fields are required',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $faq->question = $request->question;
            $faq->answer = $request->answer;
            $faq->update_ip = $request->ip();
            $faq->update_by = Auth::user()->name;
            $faq->save();
    
            return response()->json([
                'status' => true,
                'message' => 'Successfully Updated.'
            ], 200);
    
        }
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Record not found.'
                ], 404);
            }
    
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
            $faq = TblJoinOurNetworkFaqs::findOrFail($id);
    
            $faq->delete();
    
            return response()->json([
                'status' => true,
                'message' => 'Successfully Deleted.'
            ], 200);
    
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'status' => false,
                    'message' => 'Record not found.'
                ], 404);
            }
    
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
            TblJoinOurNetworkFaqs::whereIn('id', $ids)->delete();

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
        $faq = TblJoinOurNetworkFaqs::findOrFail($id);

        $request->validate([
            'status' => 'required|boolean',
        ]);

        $faq->status = $request->status;

        if ($faq->save()) {
            return response()->json([
                'status' => true,
                'message' => 'Status updated successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Something Went Wrong'
            ], 400);
        }
    } catch (\Exception $e) {
        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => 'Record not found.'
            ], 404);
        }

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
            TblJoinOurNetworkFaqs::where('id', $id)->update(['position' => $index]);
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
