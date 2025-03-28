<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblAboutUs;


class AboutusController extends Controller
{
    public function index(){
          $aboutus = TblAboutUs::all();
        return view("frontend.aboutus.aboutus",compact('aboutus'));
    }
}
