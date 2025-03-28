<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class insert_data_in_tbl_states extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tbl_states = array(
            array('country_id' => '97','NAME' => 'Andhra Pradesh','state_code' => '28','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Assam','state_code' => '18','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Arunachal Pradesh','state_code' => '12','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Bihar','state_code' => '10','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Jammu & Kashmir','state_code' => '1','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Kerala','state_code' => '32','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Madhya Pradesh','state_code' => '23','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Maharashtra','state_code' => '27','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Manipur','state_code' => '14','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Meghalaya','state_code' => '17','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Mizoram','state_code' => '15','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Nagaland','state_code' => '13','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Odisha','state_code' => '21','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Rajasthan','state_code' => '8','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Sikkim','state_code' => '11','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Tamil Nadu','state_code' => '33','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Tripura','state_code' => '16','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Pondicherry','state_code' => '34','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Lakshdweep','state_code' => '31','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Daman & Diu','state_code' => '25','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Dadra & Nagar Haveli','state_code' => '26','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Chandigarh','state_code' => '4','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Andaman & Nicobar','state_code' => '35','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Uttarakhand','state_code' => '5','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Jharkhand','state_code' => '20','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Chattisgarh','state_code' => '22','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Delhi','state_code' => '7','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Punjab','state_code' => '3','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Karnataka','state_code' => '29','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Gujarat','state_code' => '24','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Uttar Pradesh','state_code' => '9','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Haryana','state_code' => '6','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Goa','state_code' => '30','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'West Bengal','state_code' => '19','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Himachal Pradesh','state_code' => '2','STATUS' => '1'),
            array('country_id' => '97','NAME' => 'Telangana','state_code' => '36','STATUS' => '1')
          );

          DB::table("tbl_states")->insert($tbl_states);


          
    }
}
