<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyPrice;
use App\Models\TblHome;
use App\Services\HyperGuestService;
use App\helper\MasterHelper;

class HyperGuestController extends Controller
{
    protected $hyperguest;

    public function __construct(HyperGuestService $hyperguest)
    {
        $this->hyperguest = $hyperguest;
    }

    // hyperguestbooking
    
    public function hyperGuestResponseNew(Request $request){
        $data = file_get_contents('php://input');
        Storage::disk('local')->put('hyperguest/hyperguest_'.time().'.txt', $data);
        
        if($request->missing('webhook_username')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        if($request->missing('webhook_password')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }

        
        if($request->webhook_username !=env('HYPER_GUEST_WEBHOOK_USERNAME')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        if($request->webhook_password != env('HYPER_GUEST_WEBHOOK_PASSWORD')){
            return response()->json([
                'status' => false,
                'message' => "Invalid Credentials!",
                
            ], 403);
        }
        
        $data = file_get_contents('php://input');
    
        libxml_use_internal_errors(true);
        $xmlObject = simplexml_load_string($data);
    
        if ($xmlObject === false) {
            return response()->json(['error' => 'Invalid XML format'], 400);
        }
    
        $xmlObject->registerXPathNamespace('soap', 'http://www.w3.org/2003/05/soap-envelope');
        $xmlObject->registerXPathNamespace('ota', 'http://www.opentravel.org/OTA/2003/05');
    
        $body = $xmlObject->xpath('//soap:Body');
    
        if (empty($body)) {
            return response()->json(['error' => 'SOAP Body not found'], 400);
        }
    
        $bodyContent = $body[0]->asXML();
        $parsedXml = simplexml_load_string($bodyContent, 'SimpleXMLElement', LIBXML_NOCDATA);
        
        $resultArray = json_decode(json_encode($parsedXml), true);
        // dd($resultArray);
        if($resultArray){
            $echoToken = $resultArray['OTA_HotelResNotifRQ']['@attributes']['EchoToken'];
            $ResStatus = $resultArray['OTA_HotelResNotifRQ']['@attributes']['ResStatus'];
            $TimeStamp = $resultArray['OTA_HotelResNotifRQ']['@attributes']['TimeStamp'];
            $CreateDateTime = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['@attributes']['CreateDateTime'];
            $dateMonth = date('Y/m', strtotime($CreateDateTime));
            $Type = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['UniqueID']['@attributes']['Type'];
            $ID = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['UniqueID']['@attributes']['ID'];
            $rand = rand(999999999,6);
                
            $hyperguestid = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['BasicPropertyInfo']['@attributes']['HotelCode'];
            $total_amount = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['Total']['@attributes']['AmountBeforeTax'];
            $payable_amount = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['Total']['@attributes']['AmountAfterTax'];
            // $no_of_adult = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['GuestCounts']['GuestCount']['@attributes']['Count'];
            $guestCounts = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['GuestCounts']['GuestCount'];

            // dd($hyperguestid);
            $no_of_adult = 0;
            $no_of_child = 0;
            
            if (isset($guestCounts['@attributes'])) {
                // Single entry case, convert it into an array for consistency
                $guestCounts = [$guestCounts];
            }
            
            foreach ($guestCounts as $guest) {
                $attributes = $guest['@attributes'];
            
                if ($attributes['AgeQualifyingCode'] == "10") {
                    $no_of_adult = $attributes['Count'];
                } elseif ($attributes['AgeQualifyingCode'] == "8") {
                    $no_of_child = $attributes['Count'];
                }
            }
            // dd($no_of_adult);
            // booking save
            
            if (isset($resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation'])) {
                $reservation = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation'];
                $roomStay = $reservation['RoomStays']['RoomStay'] ?? [];
                
                // dd($roomStay['RoomRates']['RoomRate']['@attributes']['RoomTypeCode']);
                $home  = TblHome::where('hyper_guest_id', $hyperguestid)->first();
                $date_difference_count = MasterHelper::getDateDifference($roomStay['TimeSpan']['@attributes']['Start'], $roomStay['TimeSpan']['@attributes']['End']);

                $propertyBooking = [];
                if ($home) {
                    $propertyBooking['location_id'] = $home->location_id;
                    $propertyBooking['property_id'] = $home->id;
                    $propertyBooking['total_amount'] = $total_amount;
                    $propertyBooking['payable_amount'] = $payable_amount;
                    $propertyBooking['booking_id'] = $ID;
                   
                    $propertyBooking['booking_status'] = 'paid';
                   
                    $propertyBooking['booking_created_by'] = 'ru';
                    $propertyBooking['booking_from'] = 'hyperguest';
                    if($ResStatus == 'Commit'){
                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                    }else{
                    $propertyBooking['ru_booking_status'] = 'Canceled';
                    }
                    $propertyBooking['type'] = 'Location';
                    $propertyBooking['channel'] = 'HyperGuest';
                    $propertyBooking['no_of_adult'] = $no_of_adult;
                    $propertyBooking['no_of_children'] = $no_of_child;
                    
                    $profile = $reservation['ResGuests']['ResGuest']['Profiles']['ProfileInfo']['Profile']['Customer'] ?? [];
                    $propertyBooking['customer_detail'] = json_encode([
                        'first_name' => $profile['PersonName']['GivenName'] ?? '',
                        'last_name' => $profile['PersonName']['Surname'] ?? '',
                        'email' => $profile['Email'] ?? '',
                        'mobile_number' => $profile['Telephone']['@attributes']['PhoneNumber'] ?? ''
                    ]);
                    
                
                    $propertyBooking['checkin_date'] = $roomStay['TimeSpan']['@attributes']['Start'] ?? null;
                    $propertyBooking['checkout_date'] = $roomStay['TimeSpan']['@attributes']['End'] ?? null;
                    
                    // $hotelCode = $roomStay['BasicPropertyInfo']['@attributes']['HotelCode'] ?? null;
                    // $home = TblHome::where('hyper_guest_id', $hotelCode)->first();
                }
        
                // GST calculation
                $price = $payable_amount;
                $propertyBooking['paid_amount'] = $price;
                $getAppliedGst = getAppliedGst($price);
                if ($getAppliedGst) {
                    $precentageAmount = ($price * $getAppliedGst->gst_percentage) / 100;
                    $gst_amount = $precentageAmount;
                    $gstPrecentage = (int)$getAppliedGst->gst_percentage;
                    $propertyBooking['tax'] = $gstPrecentage;
                    $propertyBooking['taxable_amount'] = $gst_amount;
                }
                
                if($ResStatus == 'Commit'){
                    $propertyBooking['property_booking_status'] = 'Confirmed';
                }else{
                    $propertyBooking['property_booking_status'] = 'Canceled';
                }
        
                $count = PropertyBooking::where('booking_id', $ID)->count();
                if ($count == 0) {
                    PropertyBooking::create($propertyBooking);
                } else {
                    PropertyBooking::where('booking_id', $propertyBooking['booking_id'])->update($propertyBooking);
                }
        
                if($ResStatus == 'Commit'){
                    blockPropertyAvailabilityInRu($home->ru_property_id, $roomStay['TimeSpan']['@attributes']['Start'] , $roomStay['TimeSpan']['@attributes']['End']);
                }
                else{
                    unBlockPropertyAvailabilityInRu($home->ru_property_id, $roomStay['TimeSpan']['@attributes']['Start'], $roomStay['TimeSpan']['@attributes']['End']);

                }
                
            }
            else {
                dd('No Hotel Reservations found in the response.');
            }
        }
        $response = "<OTA_HotelResNotifRS xmlns='http://www.opentravel.org/OTA/{$dateMonth}' EchoToken='".$echoToken."'
                        TimeStamp='".$TimeStamp."'>
                        <HotelReservations>
                            <HotelReservation CreateDateTime='".date('Y-m-d H:i:s')."' ResStatus='". $ResStatus ."'>
                                <UniqueID Type='".$Type."' ID='".$ID."' />
                                <ResGlobalInfo>
                                    <HotelReservationIDs>
                                        <HotelReservationID ResID_Value='{$rand}' ResID_Type='14' />
                                    </HotelReservationIDs>
                                </ResGlobalInfo>
                            </HotelReservation>
                        </HotelReservations>
                        <Success />
                    </OTA_HotelResNotifRS>";
        
         // log request and response
        DB::table('hyperguest_logs')->insert([
            'api_request' => $data,
            'log' => $response,
            'type' => 'hyperguestBookingbywebhook',
            'status' => 'success',
        ]);
                    
        // Call availibityPush AFTER the success response
        $apiResponse = $this->availibityPush($roomStay, $hyperguestid, $ResStatus);
        // Return only the success response
        return response($response)->header('Content-Type', 'application/xml');

    }
    
    public function availibityPush($roomStay, $hyperguestid, $ResStatus){
        $hotelCode = $hyperguestid;
        $start = $roomStay['TimeSpan']['@attributes']['Start'] ?? null;
        $end = $roomStay['TimeSpan']['@attributes']['End'] ?? null;
        $invTypeCode = $roomStay['RoomRates']['RoomRate']['@attributes']['RoomTypeCode'];
        $ratePlanCode = $roomStay['RoomRates']['RoomRate']['@attributes']['RatePlanCode'];  
        $bookinglimit = 0;
        if($ResStatus == 'Commit'){
            $bookingstatus = 'Close';
        }else{
            $bookingstatus = 'Open';
        }
        return $this->hyperguest->availibityPush(
            $hotelCode,
            $start,
            $end,
            $invTypeCode,
            $ratePlanCode,
            $bookinglimit,
            $bookingstatus
        );
    }
    
    
    public function syncHyperguestAvailabilityandRate($ru_id = '3902398', $hyperguest_id = '99987')
    {
        $homes  = TblHome::select('id', 'ru_property_id', 'hyper_guest_id', 'extra_guest_charges')->where('ru_property_id', $ru_id)
                    ->where('hyper_guest_id', $hyperguest_id)
                    ->get();
        
        // $homes  = TblHome::select('id', 'ru_property_id', 'hyper_guest_id', 'extra_guest_charges')
        //             ->whereNotNull('ru_property_id')
        //             ->where('ru_property_id', '!=', '')
        //             ->whereNotNull('hyper_guest_id')
        //             ->where('hyper_guest_id', '!=', '')
        //             ->get();
    
        if ($homes->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => "Hyperguest ID not present!",
            ], 400);
        }
    
        $checkin_date = date('Y-m-d'); // Always today
        $startDate = now()->toDateString(); // today
        $endDate = now()->addMonths(6)->toDateString(); // 6 months from today
    
        foreach ($homes as $home) {
            
            $dates = RuPropertyAvailability::select('ru_property_id', 'availability_date', 'is_available')->where('ru_property_id', $home->ru_property_id)
                    ->whereBetween('availability_date', [$startDate, $endDate])
                    ->get();
                    
            $rates = RuPropertyPrice::select('ru_property_id', 'price_date', 'price', 'extra_price')->where('ru_property_id', $home->ru_property_id)
                ->whereBetween('price_date', [$startDate, $endDate])
                ->get();
                
            $ratesByDate = $rates->keyBy('price_date');
    
            // return $homes;
    
            $url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelAvailNotifRQ';
            $rate_url = 'https://hcm.hyperguest.io/api/hcm/pms/tisyastays/v1/OTA_HotelRateAmountNotifRQ';
            $bookinglimit = '1';
    
            foreach ($dates as $date) {
                $status = ($date->is_available == 'yes') ? 'Open' : 'Close';
    
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
                            <AvailStatusMessages HotelCode="' . $home->hyper_guest_id . '">
                                <AvailStatusMessage BookingLimit="' . $bookinglimit . '">
                                    <StatusApplicationControl Start="' . $date->availability_date . '" End="' . $date->availability_date . '" InvTypeCode="ROOM-01" RatePlanCode="BB" />
                                    <RestrictionStatus Status="' . $status . '" />
                                </AvailStatusMessage>
                            </AvailStatusMessages>
                        </OTA_HotelAvailNotifRQ>
                    </soap:Body>
                </soap:Envelope>';
    
                $availabilityResponse = Http::retry(3, 1000)->timeout(60)->withHeaders([
                    'Content-Type' => 'text/xml',
                ])->withBody($xmlAvailability, 'text/xml')->post($url);
                
                $allResponses[] = [
                    'type' => 'availability',
                    'date' => $date->availability_date,
                    'response' => $availabilityResponse->body(),
                ];
    
                // Rate Push
                $rate = $ratesByDate->get($date->availability_date);
                $baseRate = $rate->price ?? 0;
                // $additionalAdults = $rate->extra_price ?? 0;
                $additionalChildren = 0;
                
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
                            <RateAmountMessages HotelCode="' . $home->hyper_guest_id . '">
                                <RateAmountMessage>
                                    <StatusApplicationControl Start="' . $date->availability_date . '" End="' . $date->availability_date . '" InvTypeCode="ROOM-01" RatePlanCode="BB" />
                                    <Rates>
                                        <Rate>
                                            <BaseByGuestAmts>
                                                <BaseByGuestAmt AgeQualifyingCode="10" AmountAfterTax="' . $baseRate . '" />
                                            </BaseByGuestAmts>
                                            <AdditionalGuestAmounts>
                                                <AdditionalGuestAmount AgeQualifyingCode="10" Amount="' . $home->extra_guest_charges . '" />
                                                <AdditionalGuestAmount AgeQualifyingCode="8" Amount="' . $additionalChildren . '" />
                                            </AdditionalGuestAmounts>
                                        </Rate>
                                    </Rates>
                                </RateAmountMessage>
                            </RateAmountMessages>
                        </OTA_HotelRateAmountNotifRQ>
                    </soap:Body>
                </soap:Envelope>';
    
                    // dd($xmlRatePush);
                $rateResponse = Http::retry(3, 1000)->timeout(60)->withHeaders([
                    'Content-Type' => 'text/xml',
                ])->withBody($xmlRatePush, 'text/xml')->post($rate_url);
                
                $allResponses[] = [
                    'type' => 'rate',
                    'date' => $date->availability_date,
                    'response' => $rateResponse->body(),
                ];
            }
        }
        
        Storage::disk('local')->put(
            'hyperguestSyncData/hyperguestsync_' . time() . '.json',
            json_encode($allResponses, JSON_PRETTY_PRINT)
        );
    
        return [
            'status' => true,
            'responses' => $allResponses,
        ];
    }
    
    
}