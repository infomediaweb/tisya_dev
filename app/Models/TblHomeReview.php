<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TblHomeReview extends Model{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $fillable = [
        'home_id',
        'guest_name',
        'review_date',
        'review_type',
        'rating',
        'comment',
        'position',
        'icons_image',
        'status',
        'add_ip',
        'add_by',
        'update_ip',
        'update_by'
    ];

    protected $casts = [
      'rating' => 'array',
    ];
    
     public function reviewImages(){
    return $this->belongsTo(TblReviewImages::class, 'review_type','id');
}

    
    
    protected function img(): Attribute{
        return Attribute::make(
            get: fn (string $value=null) => (!is_null($value))?'review/'.$value:'review/no-user.png',
        );
    }

    /* public function getRatingAttribute($value){
        $explode = explode('.', $value);
        $fullRating = $explode[0];
        $halfRating = '';
        if(isset($explode[1]) && $explode[1] > 0){
            $halfRating = 1;
        }
        return array('full_rating'=>$fullRating, 'half_rating'=>$halfRating);
    } */

    protected $appends =['icon_url'];

    public function getIconUrlAttribute(){
       // return URL('/').'/storage/review/images/'.$this->icons_image;
       return $this->icons_image 
        ? URL('/').'/storage/review/images/'.$this->icons_image 
        : URL('/').'/assets/images/no-image-icon.png';
    }
}



