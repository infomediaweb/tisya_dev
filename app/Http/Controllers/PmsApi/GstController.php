<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\TblGst;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use DB;

class GstController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    
        try {
            $query = TblGst::get();
            return response()->json([
                'status' => true,
                'data' => $query,               
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
        //
        try {
            
            $addMultiItem = $request->addMultiItem;

            //print_r( $addMultiItem);

            if(count($addMultiItem) > 0 )
            {

                TblGst::truncate();
                

                foreach($addMultiItem as $rs)
                {
                    //dd($rs['slabs_start']);

                    $SQL = new TblGst();
                    $SQL->slabs_start =  $rs['slabs_start'];
                    $SQL->slabs_upto =  $rs['slabs_upto'];
                    $SQL->gst_percentage =  $rs['gst_percentage'];                   
                    $SQL->add_ip           = $request->ip();
                    $SQL->add_by           = Auth::user()->name;
                    $SQL->save();

                }

                return response([
                    'status' => true,
                    'message' => 'Successfully Submitted'
                ], 200);
                
            }
            else
            {
                return response([
                    'status' => false,
                    'message' => 'Error!, please try again later.'
                ], 400);
            }
            
        
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
    public function show(TblGst $tblGst)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TblGst $tblGst)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TblGst $tblGst)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TblGst $tblGst)
    {
        //
    }
}
