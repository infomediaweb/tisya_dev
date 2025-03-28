<?php

namespace App\Http\Controllers\PmsApi\HyperGuest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Services\HyperGuestService;
use Carbon\Carbon;

class HyperGuestAvailibilty extends Controller
{
    protected $hyperguest;

    public function __construct(HyperGuestService $hyperguest)
    {
        $this->hyperguest = $hyperguest;
    }

    /*public function availibityPush()
    {
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';

        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope/">
            <soap:Header>
                <wsse:Security soap:mustUnderstand="1" xmlns:wsse="http://schemas.xmlsoap.org/ws/2003/06/secext" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <wsse:UsernameToken>
                        <wsse:Username>tisyastays-ota</wsse:Username>
                        <wsse:Password Type="wsse:PasswordText">3g4oht2825ng0669597006295572600</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soap:Header>
            <soap:Body>
                <OTA_HotelAvailNotifRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0" EchoToken="1234">
                    <AvailStatusMessages HotelCode="99175">
                        <AvailStatusMessage BookingLimit="10">
                        <StatusApplicationControl Start="2025-02-22" End="2025-03-30" InvTypeCode="DBL" RatePlanCode="RO" />
                            <!-- Restrictions, see below -->
                        </AvailStatusMessage>
                        <AvailStatusMessage BookingLimit="10">
                        <StatusApplicationControl Start="2025-02-22" End="2025-03-30" InvTypeCode="TRPL" RatePlanCode="BB" />
                            <!-- Restrictions, see below -->
                        </AvailStatusMessage>
                        <AvailStatusMessage BookingLimit="10">
                        <StatusApplicationControl Start="2025-02-22" End="2025-03-30" InvTypeCode="SGL" RatePlanCode="RO" />
                            <!-- Restrictions, see below -->
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';

        try {
            // Send SOAP request using Laravel HTTP Client
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml',
            ])->withBody($xmlData, 'text/xml')->post($url);

            // Check if request was successful
            if ($response->successful()) {
                // Convert XML response to array
                $xmlObject = simplexml_load_string($response->body());
                $xmlObject->registerXPathNamespace('ns', 'http://www.opentravel.org/OTA/2003/05'); // Adjust namespace
                
                $data = $xmlObject->xpath('//ns:OTA_HotelAvailNotifRQ'); // Change based on actual XML structure
                // dd($data);
                return [
                    'success' => true,
                    'data' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'status' => $response->status(),
                    'message' => 'Request failed!',
                    'response' => $response->body(),
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
        return response()->json($response);
    }*/
    
    /*public function availibityPush($hotelCode = '99175', $start = '2025-02-25', $end = '2025-03-25', $invTypeCode = 'DBL', $ratePlanCode = 'BB')
    {
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
    
        $xmlData = '<?xml version="1.0" encoding="UTF-8"?>
        <soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope/">
            <soap:Header>
                <wsse:Security soap:mustUnderstand="1" xmlns:wsse="http://schemas.xmlsoap.org/ws/2003/06/secext" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                    <wsse:UsernameToken>
                        <wsse:Username>tisyastays-ota</wsse:Username>
                        <wsse:Password Type="wsse:PasswordText">3g4oht2825ng0669597006295572600</wsse:Password>
                    </wsse:UsernameToken>
                </wsse:Security>
            </soap:Header>
            <soap:Body>
                <OTA_HotelAvailNotifRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0" EchoToken="1234">
                    <AvailStatusMessages HotelCode="' . $hotelCode . '">
                        <AvailStatusMessage BookingLimit="10">
                            <StatusApplicationControl Start="' . $start . '" End="' . $end . '" InvTypeCode="' . $invTypeCode . '" RatePlanCode="' . $ratePlanCode . '" />
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml',
            ])->withBody($xmlData, 'text/xml')->post($url);
    
            if ($response->successful()) {
                $xmlObject = simplexml_load_string($response->body());
                $xmlObject->registerXPathNamespace('ns', 'http://www.opentravel.org/OTA/2003/05');
                $success = $xmlObject->xpath('//ns:Success');
    
                if (!empty($success)) {
                    $startDate = Carbon::parse($start);
                    $endDate = Carbon::parse($end);
                    $dates = [];
                    while ($startDate <= $endDate) {
                        DB::table('hyper_guest_availabilities')->updateOrInsert([
                            'hyper_guest_id' => $hotelCode,
                            'availability_date' => $startDate->format('Y-m-d'),
                        ], [
                            'is_available' => 'yes',
                            'status' => '1',
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]);
                        $startDate->addDay();
                    }
    
                    return [
                        'success' => true,
                        'message' => 'Availability data saved successfully!',
                    ];
                }
            }
    
            return [
                'success' => false,
                'message' => 'Request failed or no success element found!',
                'response' => $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    } */
    
    public function availibityPush(){
        // $hotelCode = '99175';
        // $start = new DateTime();
        // $end = (clone $start)->modify('+6 months');
        // $invTypeCode = 'DBL';
        // $ratePlanCode = 'BB';

        // $response = $this->hyperguest->availibityPush(
        //     $hotelCode,
        //     $start->format('Y-m-d'),
        //     $end->format('Y-m-d'),
        //     $invTypeCode,
        //     $ratePlanCode
        // );

        // return response()->json($response);
        // $hotelCode, $start, $end , $ratePlanCode, $amountBeforetax, $amountAftertax, $tax, $firstname, $lastname, $phone, $email, $postalcode, $address, $state, $city, $birthdate, $age )

        $hotelCode = '99175';
        $start = '26-02-2025';
        $end = '03-03-2025';
        $invTypeCode = 'DBL';
        $ratePlanCode = 'BB';
        $amountBeforetax = '2000';
        $amountAftertax = '2999';
        $tax = '999';
        $firstname = 'Testbooking';
        $lastname = '26feb';
        $phone = '1234567890';
        $email = '110085';
        $address = 'Lajpat Nagar';
        $state = 'Delhi';
        $city = 'New Delhi';
        $birthdate = '2001-06-15';
        $age = '28';

        $response = $this->hyperguest->bookPush(
            $hotelCode,
            $start,
            $end,
            $invTypeCode,
            $ratePlanCode,
            $amountBeforetax,
            $amountAftertax,
            $tax,
            $firstname,
            $lastname,
            $phone,
            $email ,
            $address,
            $state,
            $city,
            $birthdate,
            $age
        );

        return response()->json($response);
    }
    
}