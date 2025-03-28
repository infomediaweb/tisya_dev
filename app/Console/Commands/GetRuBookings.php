<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TblHome;
use App\Models\RuPropertyPrice;
use App\Models\PropertyBooking;
use App\Models\RuPropertyAvailability;
use App\helper\MasterHelper;

use Illuminate\Support\Facades\Storage;
use carbon;

class GetRuBookings extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:getRuBookings';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Ru Bookings';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);
        try {
            $dateFrom = date("Y-m-d H:i:s", strtotime("-390 minutes"));
            $dateTo = date("Y-m-d H:i:s");
        
            // $dateFrom = '2024-06-23 00:00:00';
            // $dateTo = '2024-06-25 00:00:00';
            
            $xmlReq = "<Pull_ListReservations_RQ>
                        <Authentication>
                            <UserName>".config('ru.RU_USER_NAME')."</UserName>
                            <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <DateFrom>".$dateFrom."</DateFrom>
                        <DateTo>".$dateTo."</DateTo>
                        <LocationID>0</LocationID>
                    </Pull_ListReservations_RQ>";
            $response = MasterHelper::makeXmlRequest($xmlReq);
            
            if(isset($response['data']['Reservations']['Reservation'])){
                Storage::disk('local')->put('booking_cron'.date('Y-m-d').'_'.time().'.txt', json_encode($response, true));
            }
            else{
                Storage::disk('local')->put('booking_cron_with_empty_'.date('Y-m-d').'_'.time().'.txt', json_encode($response, true));
            }
            if(isset($response['data']['Reservations']['Reservation'])){
                if(isset($response['data']['Reservations']['Reservation'][0])){
                    foreach($response['data']['Reservations']['Reservation'] as $key=>$reservation){
                        if(isset($reservation['ReservationID'])){
                            if($reservation['StatusID'] =='1' || $reservation['StatusID'] =='3'){
                             
                                $propertyBooking = array();
                                if($reservation['Creator']=='gagan@tisyastays.com'){
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
                                    $propertyBooking['channel'] = 'RU';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }

                                else if($reservation['Creator']=='agoda@rentalsunited.com'){
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
                }
                else{
                    $reservation = $response['data']['Reservations']['Reservation'];
                    if(isset($reservation['ReservationID'])){
                            if($reservation['StatusID'] =='1' || $reservation['StatusID'] =='3'){
                                $propertyBooking = array();
                                if($reservation['Creator']=='gagan@tisyastays.com'){
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
                                    $propertyBooking['channel'] = 'RU';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }
                                else if($reservation['Creator']=='agoda@rentalsunited.com'){
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


                                $price = $propertyBooking['payable_amount'];
                                $propertyBooking['paid_amount'] = $price;
                                $getAppliedGst  = getAppliedGst($price);
                                if($getAppliedGst){
                                    $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                                    $gst_amount = $precentageAmount;
                                    $gstPrecentage = (integer)$getAppliedGst->gst_percentage;
                                    $propertyBooking['tax'] = $gstPrecentage;
                                    $propertyBooking['taxable_amount'] = $gst_amount;
                                }
                              
                                $count = PropertyBooking::where('booking_id', $reservation['ReservationID'])->count();
                                if($count == 0){
                                    PropertyBooking::create($propertyBooking);
                                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($stayInfo['DateFrom'])), date('Y-m-d', strtotime($stayInfo['DateTo']))])->update(['is_available'=>'no']);
                                }
                            }
                        }
                }
                  
            }
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
