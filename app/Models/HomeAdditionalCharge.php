<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HomeAdditionalCharge extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'type_option',
        'home_id',
        'gst',
        'status'
    ];
}
