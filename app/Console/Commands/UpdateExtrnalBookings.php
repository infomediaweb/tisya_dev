<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TblHome;
use App\Models\RuPropertyPrice;
use App\helper\MasterHelper;
use App\Models\PropertyBooking;

class UpdateExtrnalBookings extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateExtrnalBookings';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import or Update RU property price';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);
        try {
            $bookings= PropertyBooking::whereIn('channel', ['MakeMyTrip', 'Airbnb', 'Booking.com'])->orderBy('id', 'desc')->whereNull('ru_response')->whereDate('created_at', now()->toDateString())->get();

            foreach($bookings as $key=>$booking){
                $xmlReqForPropertyPrice = "<Pull_GetReservationByID_RQ>
                    <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <ReservationID>".$booking->booking_id."</ReservationID>
                </Pull_GetReservationByID_RQ>";
                $response = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);
                $reservation = $response['data']['Reservation'];
        
                if(isset($reservation['ReservationID'])){
                    if($reservation['StatusID'] =='1' || $reservation['StatusID'] =='3' && $reservation['Creator'] !='gagan@tisyastays.com'){
                        if(isset($reservation['StayInfos']['StayInfo'])){
                            $reservationId = $reservation['ReservationID'];
                            $stayInfo = $reservation['StayInfos']['StayInfo'];
                            $noOfNights = (integer)(MasterHelper::getDateDifference(
                                $stayInfo['DateFrom'],
                                $stayInfo['DateTo']
                            ));
                            $basePrice = $stayInfo['Costs']['RUPrice'];
                            $booking->base_price = $basePrice;
                            $booking->no_of_nights = $noOfNights;
                            $booking->per_night_price = round($basePrice/$noOfNights);
                            $booking->ru_response = json_encode($reservation, true);
                            if(isset($stayInfo['ReservationBreakdown'])){
                                $ReservationBreakdown = $stayInfo['ReservationBreakdown'];
                                if(isset($ReservationBreakdown['ChannelBreakdown']['ChannelTotalFeesTaxes'])  && count($ReservationBreakdown['ChannelBreakdown']['ChannelTotalFeesTaxes']) >0 ){
                                    $ChannelTotalFeeTax = $ReservationBreakdown['ChannelBreakdown']['ChannelTotalFeesTaxes']['ChannelTotalFeeTax'];
                                    $CountChannelTotalFeeTax = count($ChannelTotalFeeTax);
                                    if ($CountChannelTotalFeeTax == 1) {
                                        $ChannelTotalFeeTax_Amount = $ReservationBreakdown['ChannelBreakdown']['ChannelTotalFeesTaxes']['ChannelTotalFeeTax']['@attributes']['Amount'];
                                    }
                                    else if ($CountChannelTotalFeeTax > 1) {
                                        $ChannelTotalFeeTax_Amount = 0;
                                        foreach ($ChannelTotalFeeTax as $singleChannelTotalTax) {
                                            $ChannelTotalFeeTax_Amount += $singleChannelTotalTax['@attributes']['Amount'];
                                        }
                                    }

                                    $channel_client_price = $stayInfo['Costs']['ClientPrice'];
                                    if ($channel_client_price > 0) {
                                        $channel_gst_tax_amount = $ChannelTotalFeeTax_Amount;
                                        $channel_gst_tax_percentage = round(($channel_gst_tax_amount * 100) / ($channel_client_price - $channel_gst_tax_amount));
                                    }
                                    $booking->tax = $channel_gst_tax_percentage;
                                    $booking->taxable_amount = $ChannelTotalFeeTax_Amount;
                                    $booking->total_amount = $booking->payable_amount - $ChannelTotalFeeTax_Amount;
                                    $booking->tax_amount =    $ChannelTotalFeeTax_Amount;
                                }
                            }
                            $booking->save();
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
