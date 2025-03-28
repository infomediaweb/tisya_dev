<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TblSpecialInvitation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_special_invitations';
    protected $guarded = [];
    
    public function couponCode()
    {
        return $this->hasOne(DiscountCoupon::class, 'id', 'couponcode_id');
    }
    
    
    public function discountCoupon()
{
    return $this->belongsTo(DiscountCoupon::class, 'couponcode_id', 'id'); 
}

}
