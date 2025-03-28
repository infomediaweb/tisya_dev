<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\becomeahost;
use App\Mail\EnquireSaveEmail;
use App\Mail\EnquireSaveEmailGuest;
use Illuminate\Support\Facades\Validator;
use App\Models\TblLocation;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\BookingEnquiry;
use App\Models\TblHome;


class BecomeahostController extends Controller
{
    public function index(){
        $locations = TblLocation::where('status', 1)->get();
        return view('frontend.becomeahost.becomeahost',compact('locations'));
    }

    public function becomeahost(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:10',
            'location' => 'required|string|max:255',
            'home_status' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'captcha' => 'required|string',  
        ]);
    

     
        if ($request->captcha !== $request->captcha_hidden) {
            return back()->withErrors([
                'captcha' => 'Captcha does not match.'
            ])->withInput();
        }
   
      $admin="support@tisya.tempsite.in";
    
        Mail::to($admin)->send(new becomeahost($request->all()));
        session()->flash('success', 'Thank you for submitting your Host application!
        We’ve received your details and our team will review your submission shortly.');
        
        return redirect()->route('becomeahost');
    }
    
    
    public function enquireSave(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|digits_between:6,12',
            'country_code' => 'required',
            'email' => 'required|string|email|max:255',
            'message' => 'required',
        ]);
        if($validator->passes()){
            $admin="reservations@tisyastays.com";
            $bccRecipients = ['gagan@tisyastays.com', 'sourav@tisyastays.com'];
            $property = TblHome::where('id', $request->pId)->first();
            $enquiryDetail = array();
            $enquiryDetail['location_id'] = $property->location_id;
            $enquiryDetail['property_id'] = $property->id;
            $enquiryDetail['no_of_guest'] = $request->guest;
            $enquiryDetail['no_of_night'] = $request->tot_no_of_days;
            $enquiryDetail['name'] = $request->name;
            $enquiryDetail['email'] = $request->email;
            $enquiryDetail['phone_no'] = $request->phone;
            $enquiryDetail['enquiry_message'] = $request->message;
            $enquiryDetail['country_code'] = $request->country_code;
            $enquiryDetail['total_amount'] = (integer)(str_replace(",", "", $request->total_price_multiple));
            $enquiryDetail['checkin_date'] = $request->ci_date;
            $enquiryDetail['checkout_date'] = $request->co_date;
            BookingEnquiry::create($enquiryDetail);
            
             Mail::to($admin)
            ->bcc($bccRecipients)
            ->send(new EnquireSaveEmail($request->all()));
            Mail::to($request->email)->send(new EnquireSaveEmailGuest($request->all()));
            
            DB::table('booking_guest_ids')->insert(['name'=>$request->name, 'email'=>$request->email, 'mobile_no'=>$request->phone]);
            return response()->json([
                'status' => true,
                'message' => "Enquiry Successfully",
            ]);
        } 
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
             ]);
        }    
    }
}
