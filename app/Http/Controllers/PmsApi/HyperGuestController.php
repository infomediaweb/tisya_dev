<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use App\Services\HyperGuestService;


class HyperGuestController extends Controller
{
    protected $hyperguest;

    public function __construct(HyperGuestService $hyperguest)
    {
        $this->hyperguest = $hyperguest;
    }

    public function fetchHotel()
    {
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v3/OTA_HotelAvailRQ';

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
                <OTA_HotelAvailRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0" EchoToken="1234">
                    <AvailRequestSegments>
                        <AvailRequestSegment AvailReqType="Room">
                            <HotelSearchCriteria>
                                <Criterion>
                                    <HotelRef HotelCode="99175"/>
                                </Criterion>
                            </HotelSearchCriteria>
                        </AvailRequestSegment>
                    </AvailRequestSegments>
                </OTA_HotelAvailRQ>
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
                
                $data = $xmlObject->xpath('//ns:OTA_HotelAvailRS'); // Change based on actual XML structure
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
    }
    
}