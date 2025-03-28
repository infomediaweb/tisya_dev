<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\TblState;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use DB;



class StatesController extends Controller
{
    public function getStates(Request $request)
    {
       // dd('working');
       try
       {
            $state = MasterHelper::getStateList();

            return response()->json([
                'status' => true,
                'data' => $state,
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
