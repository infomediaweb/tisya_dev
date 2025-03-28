<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyPrice;
use Maatwebsite\Excel\Facades\Excel;
use App\helper\MasterHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PropertyBooking;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyBookingPaymentRequest;
use Illuminate\Support\Collection;
use App\Models\BookingGuestId;
use App\Models\BookingEnquiry;
use App\Exports\BookingExport;
use App\Models\TblState;



class RuBookingController extends Controller{

    //---------------- This method use for registring the webhook url------//
    //---- URL for webhook registration -> https://varefamily.iws.in/ru/set/booking/webhook  ----//
    public function setBookingHandlerAPi(){
       
    }


    //---------------- This method is call after htting the url 'https://varefamily.iws.in/ru/webhook/get/bookings' ---//
    public function getBookingFromRu($hash = NULL){
        header("Content-Type:application/json");
        $data = file_get_contents('php://input');
        Storage::disk('local')->put('booking_'.time().'.txt', $data);
        $result = simplexml_load_string($data);
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);
        
        if(isset($result_array['Reservation'])){
            if(isset($result_array['Reservation']['ReservationStatusID'])){
                if($result_array['Reservation']['ReservationStatusID'] =='1' || $result_array['Reservation']['ReservationStatusID'] =='3'){
                    $propertyBooking = array();
                    if($result_array['Reservation']['Creator']=='gagan@tisyastays.com'){
                        $stayInfo =  $result_array['Reservation']['StayInfos']['StayInfo'];
                        $customerInfo =  $result_array['Reservation']['CustomerInfo'];

                        $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);

                        $propertyBooking = array();
                        $propertyBooking['location_id'] = $home->location_id;
                        $propertyBooking['property_id'] = $home->id;
                        $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
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
                    else if($result_array['Reservation']['Creator']=='bookingcom@rentalsunited.com'){
                        $stayInfo =  $result_array['Reservation']['StayInfos']['StayInfo'];
                        $customerInfo =  $result_array['Reservation']['CustomerInfo'];

                        $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
                        
                        $propertyBooking = array();
                        $propertyBooking['location_id'] = $home->location_id;
                        $propertyBooking['property_id'] = $home->id;
                        $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
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
                    else if($result_array['Reservation']['Creator']=='airbnb@rentalsunited.com'){
                        if(isset($result_array['Reservation']['StayInfos'])){
                            $stayInfo =  $result_array['Reservation']['StayInfos']['StayInfo'];
                            $customerInfo =  $result_array['Reservation']['CustomerInfo'];

                            $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                            $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);

                            $propertyBooking = array();
                            $propertyBooking['location_id'] = $home->location_id;
                            $propertyBooking['property_id'] = $home->id;
                            $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                            $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                            $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
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
                            $customerInfo =  $result_array['Reservation']['CustomerInfo'];
                            $home  = TblHome::where('ru_property_id', $result_array['Reservation']['PropertyID'])->first();
                            $date_difference_count = MasterHelper::getDateDifference($result_array['Reservation']['DateFrom'], $result_array['Reservation']['DateTo']);
                            $propertyBooking['location_id'] = $home->location_id;
                            $propertyBooking['property_id'] = $home->id;
                            $propertyBooking['total_amount'] = $result_array['Reservation']['Price'];
                            $propertyBooking['payable_amount'] = $result_array['Reservation']['Price'];
                            $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
                            $propertyBooking['booking_status'] = 'paid';
                            $propertyBooking['booking_created_by'] = 'ru';
                            $propertyBooking['booking_from'] = 'ru';
                            $propertyBooking['ru_booking_status'] = 'Confirmed';
                            $propertyBooking['type'] = 'Location';
                            $propertyBooking['channel'] = 'Airbnb';
                            $propertyBooking['no_of_adult'] = $result_array['Reservation']['NumberOfGuests'];
                            $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>($customerInfo['SurName']=='not provided'?'':$customerInfo['SurName']), 'email'=>$customerInfo['Email'] , 'mobile_number'=>(isset($customerInfo['MobilePhone'][0]))?$customerInfo['MobilePhone'][0]:'N/A'));
                            $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($result_array['Reservation']['DateFrom']));
                            $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($result_array['Reservation']['DateTo']));
                        }
                    }
                    else if($result_array['Reservation']['Creator']=='makemytrip@rentalunited.com'){
                        $stayInfo =  $result_array['Reservation']['StayInfos']['StayInfo'];
                        $customerInfo =  $result_array['Reservation']['CustomerInfo'];
                        $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                        
                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
                    
                        $propertyBooking = array();
                        $propertyBooking['location_id'] = $home->location_id;
                        $propertyBooking['property_id'] = $home->id;
                        $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
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

                    $propertyBooking['property_booking_status'] = 'Confirmed';

                    $count = PropertyBooking::where('booking_id', $result_array['Reservation']['ReservationID'])->count();
                    if($count == 0){
                        PropertyBooking::create($propertyBooking);
                    }
                    else{
                        PropertyBooking::where('booking_id', $result_array['Reservation']['ReservationID'])->update($propertyBooking);
                    }
                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($stayInfo['DateFrom'])), date('Y-m-d', strtotime($stayInfo['DateTo']))])->update(['is_available'=>'no']);
                }
            }
            else{
                dd('Lead');
            }
        }
        else{
            $bookingDetail = PropertyBooking::where('booking_id', $result_array['ReservationID'])->first();
            $home  = TblHome::where('id', $bookingDetail->property_id)->first();
            RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($bookingDetail->checkin_date)), date('Y-m-d', strtotime($bookingDetail->checkout_date))])->update(['is_available'=>'yes']);

            PropertyBooking::where('booking_id', $result_array['ReservationID'])->update(['ru_booking_status'=>'Canceled']);
        }
       
    }
}
