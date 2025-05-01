<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TblHome;
use App\Models\RuPropertyPrice;
use App\helper\MasterHelper;
use Carbon\Carbon;

class UpdatePropertyPriceBeforeToday extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdatePropertyPriceBeforeTodayJob';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update property price before today';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);
        try {
            $i = 360;
            $date_from = date('Y-m-d');
            $date_to = date('Y-m-d', strtotime($date_from . ' +'.$i.' day'));
            $list = TblHome::whereNotNull('ru_property_id')->get();
          
            if(!empty($list)){
                foreach($list as $detail){
                    $count = RuPropertyPrice::where('ru_property_id',  $detail->ru_property_id)->whereDate('updated_at', Carbon::today())->count();
                    if($count == 0){
                        $xmlReqForPropertyPrice = "<Pull_ListPropertyPrices_RQ>
                            <Authentication>
                                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                <Password>".config('ru.RU_PASSWORD')."</Password>
                            </Authentication>
                            <PropertyID>".$detail->ru_property_id."</PropertyID>
                            <DateFrom>".$date_from."</DateFrom>
                            <DateTo>".$date_to."</DateTo>
                        </Pull_ListPropertyPrices_RQ>";
                        $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);
                        if($ruPropertyPriceResponse){
                            if(isset($ruPropertyPriceResponse['data']['Prices'])){
                                if(isset($ruPropertyPriceResponse['data']['Prices']['Season'])){
                                    foreach($ruPropertyPriceResponse['data']['Prices']['Season'] as $session){
                                        $priceArray = array();
                                        $priceArray['price_date'] = $session['@attributes']['DateFrom'];
                                        $priceArray['ru_property_id'] = $detail->ru_property_id;
                                        $priceArray['price'] = $session['Price'];
                                        $priceArray['extra_price'] = $session['Extra'];
                                        $count = RuPropertyPrice::where(['price_date'=>$session['@attributes']['DateFrom'], 'ru_property_id'=> $detail->ru_property_id])->count();
                                        if($count ==0){
                                            RuPropertyPrice::create($priceArray);
                                        }
                                        else{
                                            RuPropertyPrice::where(['price_date'=>$session['@attributes']['DateFrom'], 'ru_property_id'=> $detail->ru_property_id])->update(['price'=>$session['Price']]);
                                        }
                                    }
                                }
                            }
                        }
                    }    
                   
                }
                echo 'Done';
            }
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }

    }
}
