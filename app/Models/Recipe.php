<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;
    use HasUuids;

   
    public $table = 'recipe';
    protected $fillable = [
        'title',
        'body',
        'video'
        
    ];
    
    public function images()
    {
     return $this->hasMany('App\Image', 'recipe_id');
    }

    public function user()
    {
//        return $this->belongsTo(User::class);
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
