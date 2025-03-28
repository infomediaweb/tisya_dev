<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivacyPolicy;

class PrivacyPolicyController extends Controller
{
    public function index(){
             $privacypolicyies = PrivacyPolicy::all();
        return view('frontend.privacypolicy.privacypolicy',compact('privacypolicyies'));
    }
    
   
}
