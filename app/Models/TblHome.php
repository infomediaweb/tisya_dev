<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TblHome extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $appends = ['image_full_path','pdf_full_path'];
    public function homeImageVideo(){
        return $this->hasMany(TblHomeImageVideo::class, 'home_id')->orderBy('position');
    }

    public function homeImage(){
        return $this->hasMany(TblHomeImageVideo::class, 'home_id')
                ->where('type', 'image')
                ->select('*', DB::raw(" CONCAT('/storage/home/images/', filename)  AS filepath"));
                // ->select('*', DB::raw("CASE WHEN type = 'image' THEN CONCAT('/storage/home/images/', filename) WHEN type = 'video' THEN CONCAT('/storage/home/videos/', filename) END AS filepath"));
    }


    public function locationData()
    {
        return $this->belongsTo(TblLocation::class, 'location_id', 'id');
    }


    public function homeVideo()
    {
        return $this->hasMany(TblHomeImageVideo::class, 'home_id')->where('type', 'video')
                    ->select('*', DB::raw(" CONCAT('/storage/home/video/', filename)  AS filepath"))
                    ->orderBy('position', 'asc');
    }
    
    public function tags(){
    return $this->hasMany(TblHomeTags::class, 'home_id')
                ->join('tbl_tags', 'tbl_tags.id', '=', 'tbl_home_tags.tags_id');
}


    public function homeFeatures(){
        return $this->hasMany(TblFeatures::class, 'home_id');
    }

    public function additionalCharge(){
        return $this->hasMany(HomeAdditionalCharge::class, 'home_id');
    }
    
    public function homecollections(){
        return $this->hasMany(TblHomeCollection::class, 'home_id')->orderBy('id', 'desc');
    }

    public function ownerDetail(){
        return $this->hasOne(HomeOwnerDetail::class, 'home_id');
    }

    public function homeAmenities(){
        return $this->hasMany(TblHomeAmenities::class, 'home_id');
    }

    public function homeReviews(){
        return $this->hasMany(TblHomeReview::class, 'home_id')->with('reviewImages')->orderBy('id', 'desc');
    }

    public function propertyAvailabilities(){
        return $this->hasMany(RuPropertyAvailability::class, 'ru_property_id', 'ru_property_id');
    }


    public function amenities(){
        return $this->hasMany(TblHomeAmenities::class, 'home_id')->join('tbl_amenities', 'tbl_amenities.id', '=', 'tbl_home_amenities.amenities_id');
    }

    public function images(){
        return $this->hasMany(TblHomeImageVideo::class, 'home_id', 'id')
            ->where('status', 1)
            ->whereIn('type',['image'])
            ->whereNull('deleted_at')->orderBy('position');

    }


    public function imagesVideos(){
        return $this->hasMany(TblHomeImageVideo::class, 'home_id', 'id')
            ->where('status', 1)
            ->whereIn('type',['image', 'video'])
            ->whereNull('deleted_at')->orderBy('position');

    }
    
    
    // karrar 
    
     public function imagesVideo()
{
    return $this->hasMany(TblHomeImageVideo::class, 'home_id', 'id')
        ->where('status', 1)
        ->where('type', 'video') 
        ->whereNull('deleted_at')
        ->orderBy('position');
}

    public function price(){
        // Get today's date
        $today = Carbon::today()->toDateString();
        // Fetch data where created_at is equal to today's date
        return $this->hasMany(RuPropertyPrice::class, 'ru_property_id', 'ru_property_id')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->whereDate('price_date', $today);

    }


    public function cprice(){
        // Get today's date
        $today = Carbon::today()->toDateString();
        // Fetch data where created_at is equal to today's date
        return $this->hasOne(RuPropertyPrice::class, 'ru_property_id', 'ru_property_id')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->whereDate('price_date', $today);

    }
    
    public function getImageFullPathAttribute(){
        $detail = TblHomeImageVideo::where(['home_id'=>$this->id, 'type'=>'image'])->first();
        $fullPath = URL('/').'/storage/home/images/no-image.png';
        if($detail){
            $path = URL('/').'/'.$detail->filename;
            if(file_exists($detail->filename)){
                $fullPath = $path;
            }
        }
        return $fullPath;
    }
    
    public function getPdfFullPathAttribute()
    {
        $folder = 'brochure';
        if ($this->brochure) {
            $filePath = storage_path("app/public/{$folder}/{$this->brochure}");
            if (file_exists($filePath)) {
                return asset("storage/{$folder}/{$this->brochure}");
            }
        }
        return '';
    }
    
    public function prices(){
        // Get today's date
        $today = Carbon::today()->toDateString();
        // Fetch data where created_at is equal to today's date
        return $this->hasMany(RuPropertyPrice::class, 'ru_property_id', 'ru_property_id')
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->whereDate('price_date', '>=', $today);

    }
    
    public function owner(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
    public function stateDetail(){
        return $this->hasOne(TblState::class, 'id', 'state_id')->with('companyInfo');
    }

}
