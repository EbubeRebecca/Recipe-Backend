<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class Image extends Model
{
    use HasFactory;
    public $table = 'image';
    protected $fillable = [
        'url','recipe_id'
    ];
    protected $appends = ['full_path']; // Append the derived field to the model

    public function getFullPathAttribute()
    {
        return asset('storage/'.$this->url) ;
    }
   
 
}

