<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyBookingPaymentRequest extends Model{

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'booking_request_id',
        'property_booking_id',
        'email',
        'amount',
        'payment_mode',
        'booking_request_status',
        'status',
        'note',
        'payment_link_id',
        'payment_link',
        'link_status'
    ];
}
