<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Recipe extends Model
{
    use HasFactory;
    use HasUuids;
    use HasSlug;

   
    public $table = 'recipe';
    protected $fillable = [
        'title',
        'body',
        'video',
        'slug'
        
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->slugsShouldBeNoLongerThan(50);
    }
    
    public function images()
    {
     return $this->hasMany(Image::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }


    protected $appends = ['full_video_path']; // Append the derived field to the model

    public function getFullVideoPathAttribute()
    {
        return asset('storage/'.$this->video) ;
    }
}
