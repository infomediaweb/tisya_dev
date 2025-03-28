<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class TblHomeBanner extends Model{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    
    public function homeBannerImage(){
        return $this->hasMany(TblHomeBannerImage::class, 'banner_id')->orderBy('position')
        ->where('file_type', 'image')
        ->select('*', DB::raw(" CONCAT('/storage/home_banner/', file)  AS filepath"));
    }
    public function homeBannerVideo(){
        return $this->hasOne(TblHomeBannerImage::class, 'banner_id')
        ->where('file_type', 'video')
        ->select('*', DB::raw(" CONCAT('/storage/home_banner/', file)  AS filepath"));
    }
}
     