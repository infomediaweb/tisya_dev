<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class TblHomeImageVideo extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'tbl_home_image_video';
    protected $guarded = [];

    protected $fillable = [
        'home_id',
        'type',
        'title',
        'filename',
        'default',
        'position',
        'status',
        'add_ip',
        'add_by',
        'update_ip',
        'update_by'
    ];

    protected $casts = [
        'filename' => 'string',
    ];

  public function getFilenameAttribute($value){
        $videoExtensionArray = array('flv', 'm3u8', 'mp4', 'ts', '3gp', 'mov', 'avi', 'wmv');
        $expolode = explode('.', $value);
        $folder = 'images';
        if(in_array($expolode[1], $videoExtensionArray)){
            $folder = 'video';
        }
        $path= 'storage/home/'.$folder.'/'.$value;
        if(file_exists($path)){
            $path= 'storage/home/'.$folder.'/'.$value;
        }
        else{
            $path='storage/home/images/1-home-675a8af882808_4.jpg';
        }
        return $path;
    }
    
    public function getWebsiteImageAttribute(){
        $value = $this->attributes['filename'] ?? null; 
        if (!$value) {
            return asset('assets/images/noimage-property.jpg'); 
        }

        $videoExtensionArray = ['flv', 'm3u8', 'mp4', 'ts', '3gp', 'mov', 'avi', 'wmv'];
        $exploded = explode('.', $value);
        $extension = strtolower($exploded[1] ?? ''); 
        $folder = in_array($extension, $videoExtensionArray) ? 'video' : 'images';

        $path = 'storage/home/small/' . $value;

        return file_exists(public_path($path)) ? url($path) : asset('assets/images/noimage-property.jpg');
    }
    
    public function getMediumImageAttribute(){
        $value = $this->attributes['filename'] ?? null; 
        if (!$value) {
            return asset('assets/images/noimage-property.jpg'); 
        }

        $videoExtensionArray = ['flv', 'm3u8', 'mp4', 'ts', '3gp', 'mov', 'avi', 'wmv'];
        $exploded = explode('.', $value);
        $extension = strtolower($exploded[1] ?? ''); 
        $folder = in_array($extension, $videoExtensionArray) ? 'video' : 'images';

        $path = 'storage/home/medium/' . $value;

        return file_exists(public_path($path)) ? url($path) : asset('assets/images/noimage-property.jpg');
    }
}
