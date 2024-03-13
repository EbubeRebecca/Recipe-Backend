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

   
 
}

