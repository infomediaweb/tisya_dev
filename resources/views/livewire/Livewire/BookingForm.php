<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TblLocation;

class BookingForm extends Component{

    public $locations;
    public $destination;
    public $arrival;
    public $departure;
    public $guests;

    public $location;

    public $guestOptions;

    public function mount(){
       $this->locations = TblLocation::where('status', 1)->whereNull('deleted_at')->get();

       $this->guestOptions = array(
            0=>array(
               'title'=>'Adults',
               'sub_title'=>'Ages 18+',
               'count'=>0
            ),

            1=>array(
                'title'=>'Children',
                'sub_title'=>'Ages 6 -17',
                'count'=>0
            ),

            2=>array(
                'title'=>'Infants',
                'sub_title'=>'Under 5',
                'count'=>0
            )
       );
    }


    public function updateGuestCount($i, $type){
        $guest_count = $this->guestOptions[$i]['count'];
        if($type == 'add'){
           $guest_count = $guest_count + 1;
           $this->guestOptions[$i]['count'] = $guest_count;
        }
        else{
            if($guest_count > 0){
                $guest_count = $guest_count - 1;
                $this->guestOptions[$i]['count'] = $guest_count;
            }
        }
    }



    public function submit(){
        dd($this->destination,  $this->arrival, $this->departure, $this->guestOptions);

    }


    public function render(){
        return view('livewire.booking-form');
    }
}
