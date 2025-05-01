<?php

namespace App\Livewire;
use Livewire\Component;
use GuzzleHttp\Client;

class XmlRequestsController extends Component{

    public function mount(){
        $client = new Client();
        $xml = "<Pull_ListPropTypes_RQ>
                    <Authentication>
                        <UserName>Info@varefamily.com</UserName>
                        <Password>Vare@cavalexa24</Password>
                    </Authentication>
                </Pull_ListPropTypes_RQ>";
        try{
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-type: application/x-www-form-urlencoded; charset=utf-8'
            ));
            curl_setopt($curl, CURLOPT_URL,'https://rm.rentalsunited.com/api/Handler.ashx');
            curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
            if ($xml != ""){
                if(is_array($xml)){
                   $xml = implode("&",$xml);
                }
                curl_setopt($curl, CURLOPT_POST,1);
                curl_setopt($curl, CURLOPT_POSTFIELDS,$xml);
            }
            $xml_result=curl_exec($curl);
            $result = simplexml_load_string($xml_result);
            curl_close($curl);
            $data = array('success'=>true, 'message'=>'Listed successfully', 'code'=>200, 'data'=>$result);
        }
        catch(Exception $e){
            $data = array('success'=>false, 'message'=>$e->getmessge(), 'code'=>500, 'data'=>null);
        }
        return $data;
    }

    public function render(){
        return view('livewire.index');
    }
}
