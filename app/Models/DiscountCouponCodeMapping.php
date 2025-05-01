<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCouponCodeMapping extends Model{
    use HasFactory;
    protected $table = 'discount_coupon_code_mappings';
    protected $fillable = [
        'code',
        'discount_coupon_id',
        'extra_price',
        'price_date',
        'status'
    ];
    
    public function discountCoupon(){
        return $this->hasOne(DiscountCoupon::class, 'id', 'discount_coupon_id');
    }
}
