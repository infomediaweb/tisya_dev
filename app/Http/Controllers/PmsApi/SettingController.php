<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TblSitesetting;


class SettingController extends Controller{

    public function allowGstSetting(Request $request){
        try {
            return response()->json([
                'status' => true,
                'data' => $data,
                'message' => 'Successfully retrieved.'
            ], 200);
        }
        catch(Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
