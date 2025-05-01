<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\landingpage;
use Illuminate\Support\Facades\Http;
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
        'no-of-people'=>'required|not_in:0',
        'budget'=>'required',
        'captcha' => 'required|string', 
    ]);

  if (strtoupper($request->captcha) !== strtoupper($request->captcha_hidden)) {
        return response()->json([
            'success' => false,
            'errors' => ['captcha' => 'Captcha does not match.']
        ], 422);
    }

     $admin = "reservations@tisyastays.com";
     $bccRecipients = ['gagan@tisyastays.com', 'sourav@tisyastays.com', 'eltonreubendsouza@gmail.com', 'chelseaffdes@gmail.com'];

     Mail::to($admin)
         ->bcc($bccRecipients)
         ->send(new landingpageenquiery($request->all()));


           // === SEND API to NEODOVE ===
               // $detailText = "Check-in: " . $request->ci_date . ", Check-out: " . $request->co_date . ", Property name: " . ($request->property_name ?? '');

                 $detailText = "Check-in: " . $request->input('check-in') . 
                   ", Check-out: " . $request->input('check-out') . 
                   ", No of people: " . ($request->input('no-of-people') ?? '') . 
                   ", Budget: " . ($request->input('budget') ?? '');

                  $message  = 'From Landing Page';

               
                
                $payload = [
                    "name" => $request->name,
                    "mobile" => $request->phone,
                    "email" => $request->email,
                    "detail" => $detailText,
                    "detail2" => $message,
                ];
                

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://75703d54-e40e-4f16-8bb9-f49421778dd9.neodove.com/integration/custom/c41c7b2d-6baf-41fa-a38c-068abf9194bc/leads",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode($payload),
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json"
                    ],
                ]);


                $response = curl_exec($curl);
             //   dd($response);
                $err = curl_error($curl);
                curl_close($curl);

                if ($err) {
                    \Log::error('Neodove API Error: ' . $err);
                } else {
                    \Log::info('Neodove API Response: ' . $response);
                }
                // === END API ===





    return response()->json([
        'success' => true,
        'message' => 'Thank you for submitting your enquiry! We’ve received your details and our team will review your submission shortly.'
    ]);
}

}
