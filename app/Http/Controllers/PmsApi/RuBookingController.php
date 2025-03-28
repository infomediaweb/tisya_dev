<?php

namespace App\Http\Controllers\PmsApi;

use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyPrice;
use App\Models\BookingGuestId;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingExport;
use App\helper\MasterHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PropertyBooking;
use Illuminate\Support\Facades\Storage;



class RuBookingController extends Controller{

    //---------------- This method use for registring the webhook url------//
    //---- URL for webhook registration -> https://varefamily.iws.in/ru/set/booking/webhook  ----//
    public function setBookingHandlerAPi(){
        $reqXml = "<LNM_PutHandlerUrl_RQ>
                    <Authentication>
                       
                    </Authentication>
                   
                </LNM_PutHandlerUrl_RQ>";
        $xmlResponse = MasterHelper::makeXmlRequest($reqXml);
        dd($xmlResponse);
    }


    //---------------- This method is call after htting the url 'https://varefamily.iws.in/ru/webhook/get/bookings' ---//
    public function getBookingFromRu($hash = NULL){
        Storage::disk('local')->put('before_booking.txt', 'Creating Booking File');
        // header("Content-Type:application/json");
        // $data = file_get_contents('php://input');
        // Storage::disk('local')->put('booking_'.time().'.txt', $data);
        // dd('Data->', $data);
    }
}
