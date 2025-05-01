<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TblLocation;
use App\Models\TblOurDifference;
use App\Models\TblSpecialInvitation;
use App\Models\TblHomeBanner;
use App\Models\TblSecondHome;
use App\Models\TblBlog;


class JoinOurNetworkController extends Component{
    
    public $home_banner;

   
    public function mount($id){

        $this->home_banner = TblHomeBanner::get();
        

    }
   
    public function show(){
        
            echo "this is data show function";
    }
    public function render(){
        return view('livewire.join-our-network');
    }
}
