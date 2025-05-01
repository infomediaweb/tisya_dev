<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TblHome;
use App\Models\PropertyBooking;
use App\helper\MasterHelper;
use App\Models\RuPropertyAvailability;
use Illuminate\Support\Carbon;

class UpdatePropertyAvailability extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'propertyAvailabilityJob';
    /**
     * The console command description. 
     *
     * @var string
     */
    protected $description = 'Change property availability in ru';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);
        set_time_limit(0);
        try {
            $i = 365;
     
            $date_from = date('Y-m-d');
            $date_to = date('Y-m-d', strtotime($date_from . ' +'.$i.' day'));
            $list = TblHome::whereNotNull('ru_property_id')->get();
            if(!empty($list)){
                
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
                                $detailA = RuPropertyAvailability::firstOrNew(['availability_date'=>$calendra['@attributes']['Date'], 'ru_property_id'=>$detail['ru_property_id']]);
                                $detailA->is_available = $isAvailable;
                                $detailA->availability_date = $calendra['@attributes']['Date'];
                                $detailA->ru_property_id = $detail['ru_property_id'];
                                $detailA->updated_at = date('Y-m-d h:i:s');
                                $detailA->save();
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
}
