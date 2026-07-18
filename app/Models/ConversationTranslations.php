<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationTranslations extends Model
{
    protected $table = 'conversation_translations';
    public $timestamps = false;
    protected $fillable = ['name', 'sub_title'];

    protected $appends = ['sub_title_web'];

    /**
     * Get full link when return file
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getSubTitleAttribute()
    {
        return array_key_exists('sub_title', $this->attributes) ? media_url_web($this->attributes['sub_title']) : null;
    }

    /**
     * Generate https link for web
     * @return string|null
     */
    public function getSubTitleWebAttribute()
    {
        return array_key_exists('sub_title', $this->attributes) ? media_url_web($this->attributes['sub_title']) : null;
    }
}
