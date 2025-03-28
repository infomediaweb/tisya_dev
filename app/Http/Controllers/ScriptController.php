<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyPrice;
use App\Models\BookingGuestId;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingExport;
use App\helper\MasterHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Rmunate\Utilities\SpellNumber;
use App\Models\TblCompany;
use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use App\Mail\BookingConfirmationEmail;
use App\Mail\BookingConfirmationEmailToAdmin;
use App\Services\RazorpayService;
use DB;
use Mail;

class ScriptController extends Controller{

    public function ru(){
        $xml = "<Push_PutAvbUnits_RQ>
                            <Authentication>
                                <UserName</UserName>
                                <Password></Password>
                            </Authentication>
                            <MuCalendar PropertyID='3826813'>
                                <Date From='2024-06-25' To='2024-06-28'>
                                <U>0</U>
                                <C>3</C>
                                </Date>
                            </MuCalendar>
                        </Push_PutAvbUnits_RQ>";
        $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xml);
        dd($ruPropertyPriceResponse);
    }
    
    
    public function ruPrice(){
        set_time_limit(0);
        try {
            $list = TblHome::whereNotNull('ru_property_id')->get();
            if(!empty($list)){
                foreach($list as $detail){
                    for($i=1; $i<=180; $i++){
                        $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                        $xmlReqForPropertyPrice = "<Pull_ListPropertyPrices_RQ>
                            <Authentication>
                                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                <Password>".config('ru.RU_PASSWORD')."</Password>
                            </Authentication>
                            <PropertyID>".$detail->ru_property_id."</PropertyID>
                            <DateFrom>".$price_date."</DateFrom>
                            <DateTo>".$price_date."</DateTo>
                        </Pull_ListPropertyPrices_RQ>";
                        $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);

                        if($ruPropertyPriceResponse){
                            if(isset($ruPropertyPriceResponse['data']['Prices'])){
                                if(isset($ruPropertyPriceResponse['data']['Prices']['Season'])){
                                    if(isset($ruPropertyPriceResponse['data']['Prices']['Season']['Price'])){
                                        $priceData = array();
                                        $price = RuPropertyPrice::where(['ru_property_id' =>  $detail->ru_property_id, 'price_date' => $price_date])->first();
                                        $priceData['price'] = $ruPropertyPriceResponse['data']['Prices']['Season']['Price'];
                                        $priceData['extra_price'] = $ruPropertyPriceResponse['data']['Prices']['Season']['Extra'];
                                        $priceData['price_date'] = $price_date;
                                        $priceData['ru_property_id'] = $detail->ru_property_id;
                                        if(!empty($price)){
                                            RuPropertyPrice::where(['ru_property_id' =>  $detail->ru_property_id, 'price_date' => $price_date])->update($priceData);
                                        }
                                        else{
                                            RuPropertyPrice::create($priceData);
                                        }

                                    }
                                }
                            }
                        }
                    }
                }
            }
            echo 'Done';
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }


    public function ruAvaliability(){
        set_time_limit(0);
        try {
            DB::table('ru_property_availabilities')->delete();
            $i = 360;
            $date_from = date('Y-m-d');
            $date_to = date('Y-m-d', strtotime($date_from . ' +'.$i.' day'));
            $list = TblHome::whereNotNull('ru_property_id')->get();
            if(!empty($list)){
                $price_date=date('Y-m-d', strtotime(date('Y-m-d'). '-1 days'));
                foreach($list as $detail){
                    foreach($list as $detail){
                        $xmlReqForPropertyPrice = "<Pull_ListPropertyAvailabilityCalendar_RQ>
                                <Authentication>
                                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                    <Password>".config('ru.RU_PASSWORD')."</Password>
                                </Authentication>
                                <PropertyID>".$detail['ru_property_id']."</PropertyID>
                                <DateFrom>".$date_from."</DateFrom>
                                <DateTo>".$date_to."</DateTo>
                            </Pull_ListPropertyAvailabilityCalendar_RQ>";
                        $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);
                      
                        if(isset($ruPropertyPriceResponse['data']['PropertyCalendar']['CalDay'])){
                            $availabilityArray = array();
                            foreach($ruPropertyPriceResponse['data']['PropertyCalendar']['CalDay'] as $calendra){
                                $isAvailable =  'yes';
                                if($calendra['IsBlocked']=='true'){
                                    $isAvailable = 'no';
                                }
                                $detailA = array();
                                $detailA['is_available'] = $isAvailable;
                                $detailA['availability_date'] = $calendra['@attributes']['Date'];
                                $detailA['ru_property_id'] = $detail['ru_property_id'];
                                array_push($availabilityArray, $detailA);
                            }
                            DB::table('ru_property_availabilities')->insert($availabilityArray);
                        }
                    }
                }
            }
            echo 'Done';
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    public function bookingExport() {
        return Excel::download(new BookingExport('1'), 'bookings.xlsx');
    }
    
    
    public function pdf($id) {
        set_time_limit(0);
        $paymentRequestDetail = PropertyBookingPaymentRequest::where('id', 114)->first();
        $payload = array('booking_id'=>$paymentRequestDetail->property_booking_id, 'booking_payment_request_id'=>$paymentRequestDetail->id, 'amount'=>$paymentRequestDetail->amount, 'name'=>$paymentRequestDetail->name, 'email'=>$paymentRequestDetail->email, 'contact_no'=>'911111111111');
        RazorpayService::createPaymentLink($payload);
        
        
        // $emailData =  array('id'=>'', 'bookingDetail'=>$propertyBooking);
        // $data = Mail::to('puneet@iws.in')->send(new BookingConfirmationEmail($emailData));
        // $data = Mail::to('himanshu@iws.in')->send(new BookingConfirmationEmail($emailData));
        // dd($propertyBooking->filename);
       
        
    }

    public function getRuBookingById(){
        $reservationId =  '142232714';
        $xmlReqForPropertyPrice = "<Pull_GetReservationByID_RQ>
            <Authentication>
                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                <Password>".config('ru.RU_PASSWORD')."</Password>
            </Authentication>
            <ReservationID>".$reservationId."</ReservationID>
        </Pull_GetReservationByID_RQ>";
        $response = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);
        $reservation = $response['data']['Reservation'];

       
              
        if(isset($reservation['ReservationID'])){
            if($reservation['StatusID'] =='1' || $reservation['StatusID'] =='3'){
              
                $propertyBooking = array();
                if($reservation['Creator']=='gagan@tisyastays.com' || $reservation['Creator'] =='agoda@rentalsunited.com'){
                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                    $customerInfo =  $reservation['CustomerInfo'];

                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);

                    $propertyBooking = array();
                    $propertyBooking['location_id'] = $home->location_id;
                    $propertyBooking['property_id'] = $home->id;
                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                    $propertyBooking['booking_status'] = 'paid';
                    $propertyBooking['booking_created_by'] = 'ru';
                    $propertyBooking['booking_from'] = 'ru';
                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                    $propertyBooking['type'] = 'Location';
                    $propertyBooking['channel'] = 'Agoda';
                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                }
                else if($reservation['Creator']=='bookingcom@rentalsunited.com'){
                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                    $customerInfo =  $reservation['CustomerInfo'];

                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);

                    $propertyBooking = array();
                    $propertyBooking['location_id'] = $home->location_id;
                    $propertyBooking['property_id'] = $home->id;
                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                    $propertyBooking['booking_status'] = 'paid';
                    $propertyBooking['booking_created_by'] = 'ru';
                    $propertyBooking['booking_from'] = 'ru';
                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                    $propertyBooking['type'] = 'Location';
                    $propertyBooking['channel'] = 'Booking.com';
                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['Phone']));
                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                    
                }
                else if($reservation['Creator']=='airbnb@rentalsunited.com'){
                    if(isset($reservation['StayInfos'])){
                        $stayInfo =  $reservation['StayInfos']['StayInfo'];
                        $customerInfo =  $reservation['CustomerInfo'];

                        $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);

                        $propertyBooking = array();
                        $propertyBooking['location_id'] = $home->location_id;
                        $propertyBooking['property_id'] = $home->id;
                        $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['booking_id'] = $reservation['ReservationID'];
                        $propertyBooking['booking_status'] = 'paid';
                        $propertyBooking['booking_created_by'] = 'ru';
                        $propertyBooking['booking_from'] = 'ru';
                        $propertyBooking['ru_booking_status'] = 'Confirmed';
                        $propertyBooking['type'] = 'Location';
                        $propertyBooking['channel'] = 'Airbnb';
                        $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                        $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['Phone']));
                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                    }
                    else{
                        $customerInfo =  $reservation['CustomerInfo'];
                        $home  = TblHome::where('ru_property_id', $reservation['PropertyID'])->first();
                        $date_difference_count = MasterHelper::getDateDifference($reservation['DateFrom'], $reservation['DateTo']);
                        $propertyBooking['location_id'] = $home->location_id;
                        $propertyBooking['property_id'] = $home->id;
                        $propertyBooking['total_amount'] = $reservation['Price'];
                        $propertyBooking['payable_amount'] = $reservation['Price'];
                        $propertyBooking['booking_id'] = $reservation['ReservationID'];
                        $propertyBooking['booking_status'] = 'paid';
                        $propertyBooking['booking_created_by'] = 'ru';
                        $propertyBooking['booking_from'] = 'ru';
                        $propertyBooking['ru_booking_status'] = 'Confirmed';
                        $propertyBooking['type'] = 'Location';
                        $propertyBooking['channel'] = 'Airbnb';
                        $propertyBooking['no_of_adult'] = $reservation['NumberOfGuests'];
                        $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>($customerInfo['SurName']=='not provided'?'':$customerInfo['SurName']), 'email'=>$customerInfo['Email'] , 'mobile_number'=>(isset($customerInfo['MobilePhone'][0]))?$customerInfo['MobilePhone'][0]:'N/A'));
                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($reservation['DateFrom']));
                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($reservation['DateTo']));
                    }
                }
                else if($reservation['Creator']=='makemytrip@rentalunited.com'){
                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                    $customerInfo =  $reservation['CustomerInfo'];
                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();

                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);

                    $propertyBooking = array();
                    $propertyBooking['location_id'] = $home->location_id;
                    $propertyBooking['property_id'] = $home->id;
                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                    $propertyBooking['booking_status'] = 'paid';
                    $propertyBooking['booking_created_by'] = 'ru';
                    $propertyBooking['booking_from'] = 'ru';
                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                    $propertyBooking['type'] = 'Location';
                    $propertyBooking['channel'] = 'MakeMyTrip';
                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>'N/A'));
                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                }
                $count = PropertyBooking::where('booking_id', $reservation['ReservationID'])->count();
                if($count == 0){
                    PropertyBooking::create($propertyBooking);
                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($stayInfo['DateFrom'])), date('Y-m-d', strtotime($stayInfo['DateTo']))])->update(['is_available'=>'no']);
                }
            }
        }     
    }


    public function updateRuBookings(){
        $list = PropertyBooking::whereNull('tax')->orderBy('id', 'desc')->get(); 
        foreach($list as $booking){
            $date_difference_count = MasterHelper::getDateDifference(date('Y-m-d', strtotime($booking->checkin_date)), date('Y-m-d', strtotime($booking->checkout_date)));
            $date_difference_count = (integer)$date_difference_count;

            $price = $booking->payable_amount;
            $getAppliedGst  = getAppliedGst($price);
            if($getAppliedGst){
                $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                $gst_amount = $precentageAmount;
                $gstPrecentage = (integer)$getAppliedGst->gst_percentage;
                PropertyBooking::where('id', $booking->id)->update(['tax'=>$gstPrecentage, 'taxable_amount'=>$gst_amount]);
            }
        }
    }

}
