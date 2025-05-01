<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyPrice;

class HyperGuestService
{
    public function availibityPush($hotelCode, $start, $end , $invTypeCode , $ratePlanCode, $bookinglimit, $bookingstatus)
    {
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
        $bookinglimit = $bookinglimit ?? '1';
        $bookingstatus = $bookingstatus ?? 'Open';
        
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
                        <AvailStatusMessage BookingLimit="' . $bookinglimit . '">
                            <StatusApplicationControl Start="' . $start . '" End="' . $end . '" InvTypeCode="' . $invTypeCode . '" RatePlanCode="' . $ratePlanCode . '" />
                            
                            <RestrictionStatus Status="' . $bookingstatus . '" />
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'text/xml',
            ])->withBody($xmlData, 'text/xml')->post($url);
            
            $responseBody = $response->body();
    
            $status = str_contains($responseBody, '<Success/>') ? 'success' : 'failed';
        
            $allResponses = [
                'api_request' => $xmlData,
                'log' => $response,
                'type' => 'availability',
                'status' => $status,
            ];
        
            DB::table('hyperguest_logs')->insert($allResponses);
    
            // if ($response->successful()) {
            //     $xmlObject = simplexml_load_string($response->body());
            //     $xmlObject->registerXPathNamespace('ns', 'http://www.opentravel.org/OTA/2003/05');
            //     $success = $xmlObject->xpath('//ns:Success');
    
            //     if (!empty($success)) {
            //         // $startDate = Carbon::parse($start);
            //         // $endDate = Carbon::parse($end);
            //         // $dates = [];
            //         // while ($startDate <= $endDate) {
            //         //     DB::table('hyper_guest_availabilities')->updateOrInsert([
            //         //         'hyper_guest_id' => $hotelCode,
            //         //         'availability_date' => $startDate->format('Y-m-d'),
            //         //     ], [
            //         //         'is_available' => 'yes',
            //         //         'status' => '1',
            //         //         'updated_at' => now(),
            //         //         'created_at' => now(),
            //         //     ]);
            //         //     $startDate->addDay();
            //         // }
    
            //         return [
            //             'success' => true,
            //             'message' => 'Availability push successfully!',
            //         ];
            //     }
            // }
    
            // return [
            //     'success' => false,
            //     'message' => 'Request failed or no success element found!',
            //     'response' => $response->body(),
            // ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    public function ratePush($hotelCode, $start, $end , $invTypeCode, $ratePlanCode, $amountAftertax, $extraGuestPrice)
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
                                <Rate>  
                                    <BaseByGuestAmts>
                                        <BaseByGuestAmt AgeQualifyingCode="10" AmountAfterTax="'. $amountAftertax .'"/>
                                    </BaseByGuestAmts>
                                    <AdditionalGuestAmounts>
                                        <AdditionalGuestAmount AgeQualifyingCode="10" Amount="' . $extraGuestPrice . '" />
                                        <AdditionalGuestAmount AgeQualifyingCode="8" Amount="0" />
                                    </AdditionalGuestAmounts>
                                </Rate>
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
            
            $responseBody = $response->body();
    
            $status = str_contains($responseBody, '<Success/>') ? 'success' : 'failed';
        
            $allResponses = [
                'api_request' => $xmlData,
                'log' => $response,
                'type' => 'rate',
                'status' => $status,
            ];
        
            DB::table('hyperguest_logs')->insert($allResponses);
    
            // if ($response->successful()) {
            //     $xmlObject = simplexml_load_string($response->body());
            //     $xmlObject->registerXPathNamespace('ns', 'http://www.opentravel.org/OTA/2003/05');
            //     $success = $xmlObject->xpath('//ns:Success');
    
            //     if (!empty($success)) {
            //         // $startDate = Carbon::parse($start);
            //         // $endDate = Carbon::parse($end);
            //         // $dates = [];
            //         // while ($startDate <= $endDate) {
            //         //     DB::table('hyper_guest_price')->updateOrInsert([
            //         //         'hyper_guest_id' => $hotelCode,
            //         //         'price_date' => $startDate->format('Y-m-d'),
            //         //     ], [
            //         //         'price' => $amountAftertax,
            //         //         'status' => '1',
            //         //         'updated_at' => now(),
            //         //         'created_at' => now(),
            //         //     ]);
            //         //     $startDate->addDay();
            //         //     // $dates[] = [
            //         //     //     'hyper_guest_id' => $hotelCode,
            //         //     //     'price' => $amountAftertax,
            //         //     //     'price_date' => $startDate->format('Y-m-d'),
            //         //     //     'status' => '1',
            //         //     //     'created_at' => now(),
            //         //     //     'updated_at' => now(),
            //         //     // ];
            //         //     $startDate->addDay();
            //         // }
    
            //         // DB::table('hyper_guest_price')->insert($dates);
    
            //         return [
            //             'success' => true,
            //             'message' => 'Ratepush data saved successfully!',
            //         ];
            //     }
            // }
    
            // return [
            //     'success' => false,
            //     'message' => 'Request failed or no success element found!',
            //     'response' => $response->body(),
            // ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    
    // hyperguest sync code start
    
    public function syncHyperguestAvailability($status, $dateFrom, $dateTo, $hyperGuestId){
        $status = ($status == 'yes') ? 'Open' : 'Close';
    
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
    
        // Availability Push
        $xmlAvailability = '<?xml version="1.0" encoding="UTF-8"?>
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
                    <AvailStatusMessages HotelCode="' . $hyperGuestId . '">
                        <AvailStatusMessage BookingLimit="1">
                            <StatusApplicationControl Start="' . $dateFrom. '" End="' . $dateTo . '" InvTypeCode="ROOM-01" RatePlanCode="BB" />
                            <RestrictionStatus Status="' . $status . '" />
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        $availabilityResponse = Http::retry(3, 1000)->timeout(60)->withHeaders([
            'Content-Type' => 'text/xml',
        ])->withBody($xmlAvailability, 'text/xml')->post($url);
    
        $responseBody = $availabilityResponse->body();
    
        $status = str_contains($responseBody, '<Success/>') ? 'success' : 'failed';
    
        $allResponses = [
            'api_request' => $xmlAvailability,
            'log' => $responseBody,
            'type' => 'availability',
            'status' => $status,
        ];
    
        DB::table('hyperguest_logs')->insert($allResponses);
                       
    }
    
    public function syncHyperguestRates($baseprice, $extraGuestCharges, $dateFrom, $dateTo, $hyperGuestId){
    
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelRateAmountNotifRQ';
    
        // Availability Push
        $xmlRatePush = '<?xml version="1.0" encoding="UTF-8"?>
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
                            <RateAmountMessages HotelCode="' . $hyperGuestId . '">
                                <RateAmountMessage>
                                    <StatusApplicationControl Start="' . $dateFrom . '" End="' . $dateTo . '" InvTypeCode="ROOM-01" RatePlanCode="BB" />
                                    <Rates>
                                        <Rate>
                                            <BaseByGuestAmts>
                                                <BaseByGuestAmt AgeQualifyingCode="10" AmountAfterTax="' . $baseprice . '" />
                                            </BaseByGuestAmts>
                                            <AdditionalGuestAmounts>
                                                <AdditionalGuestAmount AgeQualifyingCode="10" Amount="' . $extraGuestCharges . '" />
                                                <AdditionalGuestAmount AgeQualifyingCode="8" Amount="0" />
                                            </AdditionalGuestAmounts>
                                        </Rate>
                                    </Rates>
                                </RateAmountMessage>
                            </RateAmountMessages>
                        </OTA_HotelRateAmountNotifRQ>
                    </soap:Body>
                </soap:Envelope>';
    
        $rateResponse = Http::retry(3, 1000)->timeout(60)->withHeaders([
            'Content-Type' => 'text/xml',
        ])->withBody($xmlRatePush, 'text/xml')->post($url);
    
        $responseBody = $rateResponse->body();
    
        $status = str_contains($responseBody, '<Success/>') ? 'success' : 'failed';
    
        $allResponses = [
            'api_request' => $xmlRatePush,
            'log' => $responseBody,
            'type' => 'rate',
            'status' => $status,
        ];
    
        DB::table('hyperguest_logs')->insert($allResponses);
                       
    }
    
    // hyperguest sync code end
    
    // hyperguest block/unblock code start
    
    public function blockHyperguestAvailabilityfromRU($hyperguest_id, $checkin_date, $checkout_date){
    
        if (empty($hyperguest_id)) {
            return false;
        }
        
        $home  = TblHome::where('hyper_guest_id', $hyperguest_id)->first();
    
        if (!$home) {
            return false;
        }
       
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
        // $bookinglimit = '1';
        $status = 'Close';
    
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
                    <AvailStatusMessages HotelCode="' . $hyperguest_id . '">
                        <AvailStatusMessage BookingLimit="1">
                            <StatusApplicationControl Start="' . $checkin_date . '" End="' . $checkout_date . '" InvTypeCode="ROOM-01" RatePlanCode="BB" />
                            
                            <RestrictionStatus Status="' . $status . '" />
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        $rateResponse = Http::withHeaders([
            'Content-Type' => 'text/xml',
        ])->withBody($xmlData, 'text/xml')->post($url);
        
        $responseBody = $rateResponse->body();
        
        $status = str_contains($responseBody, '<Success/>') ? 'success' : 'failed';
    
        $allResponses = [
            'api_request' => $xmlData,
            'log' => $responseBody,
            'type' => 'blockavailibilityfromru',
            'status' => $status,
        ];
    
        DB::table('hyperguest_logs')->insert($allResponses);
    
        // return true;
    }

    public function unBlockHyperguestAvailabilityfromRU($hyperguest_id, $checkin_date, $checkout_date){
       
        if (empty($hyperguest_id)) {
            return false;
        }
        
        $home  = TblHome::where('hyper_guest_id', $hyperguest_id)->first();
    
        if (!$home) {
            return false;
        }
       
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
        // $bookinglimit = '1';
        $status = 'Open';
    
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
                    <AvailStatusMessages HotelCode="' . $hyperguest_id . '">
                        <AvailStatusMessage BookingLimit="1">
                            <StatusApplicationControl Start="' . $checkin_date . '" End="' . $checkout_date . '" InvTypeCode="ROOM-01" RatePlanCode="BB" />
                            
                            <RestrictionStatus Status="' . $status . '" />
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        $rateResponse = Http::withHeaders([
            'Content-Type' => 'text/xml',
        ])->withBody($xmlData, 'text/xml')->post($url);
    
        $responseBody = $rateResponse->body();
        
        $status = str_contains($responseBody, '<Success/>') ? 'success' : 'failed';
    
        $allResponses = [
            'api_request' => $xmlData,
            'log' => $responseBody,
            'type' => 'blockavailibilityfromru',
            'status' => $status,
        ];
    
        DB::table('hyperguest_logs')->insert($allResponses);
        // return true;
    }
    
    public function blockHyperguestAvailability($hyperguest_id, $checkin_date, $checkout_date){
    
        if (empty($hyperguest_id)) {
            return false;
        }
        
        $home  = TblHome::where('hyper_guest_id', $hyperguest_id)->first();
    
        if (!$home) {
            return false;
        }
       
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
        // $bookinglimit = '1';
        $status = 'Close';
    
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
                    <AvailStatusMessages HotelCode="' . $hyperguest_id . '">
                        <AvailStatusMessage BookingLimit="1">
                            <StatusApplicationControl Start="' . $checkin_date . '" End="' . $checkout_date . '" InvTypeCode="ROOM-01" RatePlanCode="BB" />
                            
                            <RestrictionStatus Status="' . $status . '" />
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        $rateResponse = Http::withHeaders([
            'Content-Type' => 'text/xml',
        ])->withBody($xmlData, 'text/xml')->post($url);
    
        // block ru
    
        blockPropertyAvailabilityInRu($home->ru_property_id, $checkin_date, $checkout_date);
        
        // return true;
        
        $responseBody = $rateResponse->body();
    
        $status = str_contains($responseBody, '<Success/>') ? 'success' : 'failed';
    
        $allResponses = [
            'api_request' => $xmlData,
            'log' => $responseBody,
            'type' => 'blockavailibility',
            'status' => $status,
        ];
    
        DB::table('hyperguest_logs')->insert($allResponses);
    }
    
    public function unBlockHyperguestAvailability($hyperguest_id, $checkin_date, $checkout_date){
       
        if (empty($hyperguest_id)) {
            return false;
        }
        
        $home  = TblHome::where('hyper_guest_id', $hyperguest_id)->first();
    
        if (!$home) {
            return false;
        }
       
        $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
        $bookinglimit = '1';
        $status = 'Open';
    
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
                    <AvailStatusMessages HotelCode="' . $hyperguest_id . '">
                        <AvailStatusMessage BookingLimit="' . $bookinglimit . '">
                            <StatusApplicationControl Start="' . $checkin_date . '" End="' . $checkout_date . '" InvTypeCode="ROOM-01" RatePlanCode="BB" />
                            
                            <RestrictionStatus Status="' . $status . '" />
                        </AvailStatusMessage>
                    </AvailStatusMessages>
                </OTA_HotelAvailNotifRQ>
            </soap:Body>
        </soap:Envelope>';
    
        $rateResponse = Http::withHeaders([
            'Content-Type' => 'text/xml',
        ])->withBody($xmlData, 'text/xml')->post($url);
    
        // unblock ru
    
        unBlockPropertyAvailabilityInRu($home->ru_property_id, $checkin_date, $checkout_date);
        
        // return true;
        
        $responseBody = $rateResponse->body();
    
        $status = str_contains($responseBody, '<Success/>') ? 'success' : 'failed';
    
        $allResponses = [
            'api_request' => $xmlData,
            'log' => $responseBody,
            'type' => 'unblockavailibility',
            'status' => $status,
        ];
    
        DB::table('hyperguest_logs')->insert($allResponses);
    }
    
    // hyperguest block/unblock code end
}