<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\conatactus;
use Illuminate\Support\Facades\Mail;
class ContactusController extends Controller
{
    public  function index(){
        return view("frontend.contactus.contactus");
    }


    public function contact(Request $request)
    {
       
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
             'mobile' => 'required|regex:/^[6-9][0-9]{9}$/',
            'message' => 'required|string|max:255',
            'captcha' => 'required|string',  
        ]);

        if ($request->captcha !== $request->captcha_hidden) {
            return back()->withErrors([
                'captcha' => 'Captcha does not match.'
            ])->withInput();
        }
            $admin=" guestrelations@tisyastays.com";
        Mail::to($admin)->send(new conatactus($request->all()));
        session()->flash('success', '"We have received your request. Our team will connect with you shortly!');
        return redirect()->route('contactus');
    }
    
}
