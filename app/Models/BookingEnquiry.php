<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BookingEnquiry extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'property_id',
        'no_of_guest',
        'no_of_night',
        'name',
        'phone_no',
        'email',
        'enquiry_message',
        'total_amount',
        'checkin_date',
        'checkout_date',
        'country_code',
    ];


    public function location(){
        return $this->belongsTo(TblLocation::class, 'location_id');
    }

    public function property(){
        return $this->belongsTo(TblHome::class, 'property_id');
    }
    
    protected $appends = ['formatted_checkin_date', 'formatted_checkout_date'];

    public function getFormattedCheckinDateAttribute()
    {
        return $this->checkin_date ? Carbon::parse($this->checkin_date)->format('d F Y') : null;
    }

    public function getFormattedCheckoutDateAttribute()
    {
        return $this->checkout_date ? Carbon::parse($this->checkout_date)->format('d F Y') : null;
    }

}
