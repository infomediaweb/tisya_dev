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

class HyperGuestRate extends Controller
{
   
    public function ratePush($hotelCode = '99175', $start = '2025-02-25', $end = '2025-03-25', $invTypeCode = 'DBL', $ratePlanCode = 'BB', $amountAftertax = '1000')
    {
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelRateAmountNotifRQ';
    
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
                <OTA_HotelRateAmountNotifRQ xmlns="http://www.opentravel.org/OTA/2003/05" Version="1.0" EchoToken="1234">
                    <RateAmountMessages HotelCode="' . $hotelCode . '">
                        <RateAmountMessage>
                        <StatusApplicationControl Start="' . $start . '" End="' . $end . '" InvTypeCode="' . $invTypeCode . '" RatePlanCode="' . $ratePlanCode . '" />
                            <Rates>
                                <BaseByGuestAmts>
                                    <BaseByGuestAmt AgeQualifyingCode="10" AmountAfterTax="'. $amountAftertax .'"/>
                                </BaseByGuestAmts>
                            </Rates>
                        </RateAmountMessage>
                    </RateAmountMessages>
                </OTA_HotelRateAmountNotifRQ>
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
                        $dates[] = [
                            'hyper_guest_id' => $hotelCode,
                            'price' => $amountAftertax,
                            'price_date' => $startDate->format('Y-m-d'),
                            'status' => '1',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $startDate->addDay();
                    }
    
                    DB::table('hyper_guest_price')->insert($dates);
    
                    return [
                        'success' => true,
                        'message' => 'Ratepush data saved successfully!',
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
    }
    
}