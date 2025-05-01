<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TblHome;
use App\Models\RuPropertyPrice;
use GuzzleHttp\Psr7\Request;
use App\helper\MasterHelper;
use App\Models\RuMinStay;
use Carbon\Carbon;
use GuzzleHttp\Client;


class SyncMinStayCommand extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SyncMinStayCommandJob';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update property min stay';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);
        try {
            $i = 180;
            $date_from = date('Y-m-d');
            $date_to = date('Y-m-d', strtotime($date_from . ' +'.$i.' day'));
            $list = TblHome::whereNotNull('ru_property_id')->get();
          
            if(!empty($list)){
                foreach($list as $detail){
                    
                    $client = new Client();
                    $headers = ['Content-Type' => 'application/xml'];
    
                    $minStay = 1;
                   
                    $body = "<Pull_ListPropertyMinStay_RQ>
                        <Authentication>
                            <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                            <Password>" . config('ru.RU_PASSWORD') . "</Password>
                        </Authentication>
                        <PropertyID>" . $detail->ru_property_id . "</PropertyID>
                        <DateFrom>".$date_from."</DateFrom>
                        <DateTo>".$date_to."</DateTo>
                    </Pull_ListPropertyMinStay_RQ>";
        
                    $request = new Request('POST', 'https://rm.rentalsunited.com/api/Handler.ashx', $headers, $body);
                    $res = $client->sendAsync($request)->wait();
                    $res->getBody();
                    $xml = new \SimpleXMLElement($res->getBody());
                    $status = (string)$xml->Status;
                   
        
                    $minStayArray = array();
                    if(isset($xml->Status) &&  (string)$xml->Status == 'Success'){
        
                        $response_id = (string)$xml->ResponseID;
                        $property_id = (string)$xml->PropertyMinStay['PropertyID'];
                        
                      
                        foreach ($xml->PropertyMinStay->MinStay as $minStay) {
                            $dates_to = date('Y-m-d', strtotime((string)$minStay['DateTo'] . '-1 day'));
                            $arr = array('from'=>(string)$minStay['DateFrom'], 'to'=>$dates_to, 'minStay'=>(string)(string)$minStay);
                            array_push($minStayArray, $arr);
                             
                        }
                    }
                    
                    $datesWithMinStay = [];
                    $currentDate = Carbon::now()->startOfDay();
                    if(isset($minStayArray[0]['from'])){
                        $firstRangeStartDate = Carbon::parse($minStayArray[0]['from']);
                        while ($currentDate->lt($firstRangeStartDate)) {
                            $dateString = $currentDate->format('Y-m-d');
                            $datesWithMinStay[$dateString] = 1;
                            $currentDate->addDay();
                        }
                        // Loop through each range
                        foreach ($minStayArray as $range) {
                            // Use Carbon for date handling
                            $startDate = Carbon::parse($range['from']);
                            $endDate = Carbon::parse($range['to']);
                            $minStay = $range['minStay'];
                
                            // Loop from start date to end date
                            while ($startDate->lte($endDate)) {
                                // Format the date as string and store with min stay
                                $dateString = $startDate->format('Y-m-d');
                                $datesWithMinStay[$dateString] = (integer)$minStay;
                
                                // Move to the next day
                                $startDate->addDay();
                            }
                        }
                    }
                    
                    if($datesWithMinStay){
                        RuMinStay::where('ru_property_id', $detail->ru_property_id)->delete();
                    }
                    foreach($datesWithMinStay as $date=>$minstay){
                        $minStay = RuMinStay::firstOrNew(['ru_property_id'=>$detail->ru_property_id, 'minstay_date'=>$date]);
                        $minStay->ru_property_id = $detail->ru_property_id;
                        $minStay->minstay_date = $date;
                        $minStay->minstay = $minstay;
                        $minStay->status = 1;
                        $minStay->save();
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
