<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblLocation extends Model
{
    use HasFactory;
    protected $table = "tbl_location";

    protected $fillable = [
        'state_id',
        'ru_location_id',
        'location_name',
        'tax',
        'status',
        'meta_title',
        'meta_description',
        'meta_keyword'
    ];


  public function state()
    {
        return $this->belongsTo(TblState::class, 'state_id', 'id');
    }
    
    public function property()
    {
        return $this->hasMany(TblHome::class, 'location_id', 'id')->with(['price', 'images', 'imagesVideos', 'tags', 'amenities', 'homeAmenities',  'homeFeatures', 'homeReviews', 'additionalCharge', 'homeImageVideo']);
    }
}
