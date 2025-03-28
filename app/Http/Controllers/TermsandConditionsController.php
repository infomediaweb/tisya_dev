<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblTermsandCondition;

class TermsandConditionsController extends Controller
{
    
     public function index(){
          $termandcondition = TblTermsandCondition::all();
        return view("frontend.termandcondition.termandcondition",compact('termandcondition'));
    }
    
}
