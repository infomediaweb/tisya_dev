<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\landingpage;
use App\Mail\landingpageenquiery;
use Illuminate\Support\Facades\Mail;

class LandingpageEnquieryController extends Controller
{
    public function landingenquireSave(Request $request)
{
    $request->validate([
        'name' => 'required',
        'phone' => 'required|digits_between:6,12',
        'email' => 'required|string|email|max:255',
        'check-in' => 'required',
        'check-out'=>'required',
        'captcha' => 'required|string', 
    ]);

   if (strtoupper($request->captcha) !== strtoupper($request->captcha_hidden)) {
        return response()->json([
            'success' => false,
            'errors' => ['captcha' => 'Captcha does not match.']
        ], 422);
    }

    $admin = "karrar@iws.in";
    $bccRecipients = ['karrar@iws.in'];

    Mail::to($admin)
        ->bcc($bccRecipients)
        ->send(new landingpageenquiery($request->all()));

    return response()->json([
        'success' => true,
        'message' => 'Thank you for submitting your enquiry! We’ve received your details and our team will review your submission shortly.'
    ]);
}

}
