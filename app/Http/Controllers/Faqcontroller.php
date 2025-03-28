<?php

namespace App\Http\Controllers;

use App\Models\TblFaq;
use Illuminate\Http\Request;


class Faqcontroller extends Controller
{
  public function faq()
{
    $faqs = TblFaq::where('status', 1)->orderBy('position', 'asc')->get();
    return view('frontend.faq.faq', compact('faqs'));
}

}
