<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\TblHome;
use App\Models\TblLocation;
use App\Models\PropertyBooking;
use App\Models\RuPropertyPrice;
use App\helper\MasterHelper;
use App\Models\RuPropertyAvailability;


class HyperGuestWebhook extends Controller
{
 
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
        if($resultArray){
            $echoToken = $resultArray['OTA_HotelResNotifRQ']['@attributes']['EchoToken'];
            $TimeStamp = $resultArray['OTA_HotelResNotifRQ']['@attributes']['TimeStamp'];
            $CreateDateTime = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['@attributes']['CreateDateTime'];
            $dateMonth = date('Y/m', strtotime($CreateDateTime));
            $Type = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['UniqueID']['@attributes']['Type'];
            $ID = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['UniqueID']['@attributes']['ID'];
            $rand = rand(999999999,6);
                
            $hyperguestid = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['BasicPropertyInfo']['@attributes']['HotelCode'];
            $total_amount = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['Total']['@attributes']['AmountBeforeTax'];
            $payable_amount = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['Total']['@attributes']['AmountAfterTax'];
            $no_of_adult = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation']['RoomStays']['RoomStay']['GuestCounts']['GuestCount']['@attributes']['Count'];
            
            // dd($no_of_adult);
            // booking save
            
            if (isset($resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation'])) {
                $reservation = $resultArray['OTA_HotelResNotifRQ']['HotelReservations']['HotelReservation'];
                $roomStay = $reservation['RoomStays']['RoomStay'] ?? [];
                
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
                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                    $propertyBooking['type'] = 'Location';
                    $propertyBooking['channel'] = 'HyperGuest';
                    $propertyBooking['no_of_adult'] = $no_of_adult;
                    
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
        
                $propertyBooking['property_booking_status'] = 'Confirmed';
        
                $count = PropertyBooking::where('booking_id', $ID)->count();
                
                if ($count == 0) {
                    PropertyBooking::create($propertyBooking);
                } else {
                    PropertyBooking::where('booking_id', $propertyBooking['booking_id'])->update($propertyBooking);
                }
        
                if ($home) {
                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)
                        ->whereBetween('availability_date', [
                            $roomStay['TimeSpan']['@attributes']['Start'],
                            $roomStay['TimeSpan']['@attributes']['End']
                        ])->update(['is_available' => 'no']);
                }
                
                // blockPropertyAvailabilityInRu($home->ru_property_id, $roomStay['TimeSpan']['@attributes']['Start'] , $roomStay['TimeSpan']['@attributes']['End']);
            } else {
                dd('No Hotel Reservations found in the response.');
            }
            
        }
        
        $response = "<OTA_HotelResNotifRS xmlns='http://www.opentravel.org/OTA/{$dateMonth}' EchoToken='".$echoToken."'
                        TimeStamp='".$TimeStamp."'>
                        <HotelReservations>
                            <HotelReservation CreateDateTime='".date('Y-m-d H:i:s')."' ResStatus='Commit'>
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

    }
    
}