<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    public $table = 'image';
    protected $fillable = [
        'url','recipe_id'
    ];
    public function product()
{
  return $this->belongsTo('App\Recipe', 'recipe_id');
}
}