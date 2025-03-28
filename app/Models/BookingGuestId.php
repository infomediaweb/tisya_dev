<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BookingGuestId extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_booking_id',
        'name',
        'email',
        'mobile_no',
        'id_proof_img',
        'checkin_date',
        'checkout_date',
        'status'
    ];
}
