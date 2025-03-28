<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblAboutUs extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function aboutInformation()
    {
        return $this->hasMany(TblAboutInformation::class, 'about_id');
    }
}
