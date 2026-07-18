<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRouteTranslations extends Model
{
    public $timestamps = false;

    protected $fillable = ['image'];

    /**
     * Get full link when return file
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getImageAttribute()
    {
        return array_key_exists('image', $this->attributes) ? media_url_web($this->attributes['image']) : null;
    }

    /**
     * Get full link when return file
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getSrcAttribute()
    {
        return array_key_exists('image', $this->attributes) ? media_url_web($this->attributes['image']) : null;
    }
}
