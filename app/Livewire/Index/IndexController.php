<?php

namespace App\Livewire\Index;

use App\Models\TblTestimonial;
use Livewire\Component;
use App\Models\TblLocation;
use App\Models\TblOurDifference;
use App\Models\TblSpecialInvitation;
use App\Models\TblHomeBanner;
use App\Models\TblSecondHome;
use App\Models\TblBlog;


class IndexController extends Component {
    
    public $home_banner;
    public $locations;
    public $diffrences;
    public $special_invitation;
    public $second_home;

    public $recent_blog;

    public $testimonials;

   
    public function mount(){

        $this->home_banner = TblHomeBanner::where('status', 1)->whereNull('deleted_at')->get();
        $this->locations = TblLocation::where('status', 1)->whereNull('deleted_at')->get();
        $this->diffrences = TblOurDifference::where('status', 1)->whereNull('deleted_at')->get();
        $this->special_invitation = TblSpecialInvitation::where('status', 1)->whereNull('deleted_at')->get();
        $this->second_home = TblSecondHome::where('status', 1)->whereNull('deleted_at')->get();
       
        $this->recent_blog = TblBlog::where('status', 1)
                             ->whereNull('deleted_at')
                             ->orderByDesc('created_at')
                             ->get();

                         
                         // Iterate over each testimonial to fetch the home name
        $this->testimonials = TblTestimonial::leftJoin('tbl_homes', 'tbl_testimonials.home_name', '=', 'tbl_homes.id')
                         ->select('tbl_testimonials.*', 'tbl_homes.home_name as home_name')
                         ->where('tbl_testimonials.status', 1)
                         ->whereNull('tbl_testimonials.deleted_at')
                         ->get();
                     
                                              


    }
   
    public function show(){
        
            echo "this is data show function";
    }
    public function render(){
        return view('livewire.index');
    }
}
 