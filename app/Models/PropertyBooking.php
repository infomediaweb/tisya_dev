<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PropertyBooking extends Model{

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'booking_id',
        'user_id',
        'location_id',
        'property_id',
        'total_amount',
        'payable_amount',
        'paid_amount',
        'tax_amount',
        'discount_amount',
        'transcation_id',
        'customer_detail',
        'provider',
        'booking_status',
        'booking_created_by',
        'no_of_adult',
        'no_of_children',
        'checkin_date',
        'checkout_date',
        'type',
        'status',
        'additional_charges',
        'initial_price',
        'gst_amount',
        'per_night_price',
        'is_invoice',
        'tax_inclusive',
        'invoice',
        'ru_booking_status',
        'booking_from',
        'channel',
        'property_booking_status',
        'razorpay_order_id',
        'applied_discount_coupon',
        'tot_additional_charge',
        'no_of_nights',
        'created_by',
    ];

    protected $appends = ['tot_guests'];

    protected function checkinDate(): Attribute{
        return Attribute::make(
            get: fn (string $value) => date('j F Y', strtotime($value)),
        );
    }

    public function home(){
        return $this->HasOne(TblHome::class, 'id', 'property_id');
    }


    public function user(){
        return $this->HasOne(User::class, 'id', 'created_by');
    }


    protected function checkoutDate(): Attribute{
        return Attribute::make(
            get: fn (string $value) => date('j F Y', strtotime($value)),
        );
    }

    protected function createdAt(): Attribute{
        return Attribute::make(
            get: fn (string $value) => date('j F Y h:i:A', strtotime($value)),
        );
    }

    public function paymentRequests(){
        return $this->hasMany(PropertyBookingPaymentRequest::class);
    }

    public function bookingGuests(){
        return $this->hasMany(BookingGuestId::class);
    }

    protected $casts = [
        'customer_detail' => 'string',
        'additional_charges' => 'string',

    ];

    public function getCustomerDetailAttribute($value){
        if($value){
            $value = json_decode($value, true);
        }
        return $value;
    }
    
   

    public function getAdditionalChargesAttribute($value){
        if($value){
            $value = json_decode($value, true);
        }
        return $value;
    }
    
    public function property(){
        return $this->hasOne(TblHome::class, 'id', 'property_id')->with(['homeImage', 'owner', 'stateDetail']);

    }

    public function getTotGuestsAttribute(){

        $totGuest = $this->no_of_adult;

        if($this->no_of_children){

            $totGuest = $totGuest + $this->no_of_children;

        }

        return $totGuest;

    }
}
