<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CancellationPolicy;

class CancellationRefundPolicyController extends Controller
{
    public function index(){
           $cancellationpolicys = CancellationPolicy::all();
        return view("frontend.cancellationrefundpolicy.cancellationrefundpolicy",compact('cancellationpolicys'));
    }
}
