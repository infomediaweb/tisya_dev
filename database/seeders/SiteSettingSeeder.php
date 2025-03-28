<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tbl_sitesettings = array(
            array('company_name' => 'Varefamily','company_prefix' => 'TM','domain_name' => 'varefamily.com','smtp'=>'mail.iws.in','auth_email'=>'nawal@iws.in','auth_email_username'=>'nawal@iws.in','auth_email_password'=>'Newpass@890','STATUS' => '1'),
        );
        DB::table("tbl_sitesettings")->insert($tbl_sitesettings);
    }
}
