<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TblHome;
use App\Models\RuPropertyPrice;
use App\helper\MasterHelper;

class UpdateRuPropertyPrice extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'urpp:job';
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
}
