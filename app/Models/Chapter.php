<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chapter extends Model
{
    use \Astrotomic\Translatable\Translatable;
    //Declare type
    const LISTENING = 5;
    const READING = 4;
    const EXERCISE = 9;

    //Declare category
    const COURSE = 1;
    const EXAM = 2;

    protected $table = 'chapter';

    protected $fillable = ['category', 'type', 'is_active', 'image'];

    public $translatedAttributes = ['name'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lessons()
    {
        return $this->belongsToMany('App\Models\Lesson', 'lesson_chapter', 'chapter_id', 'lesson_id');
    }

    public function scopeGetByCat($q, $cat)
    {
        if (isset($cat))
            return $q->where('category', $cat);
        else
            return $q->where('category', self::COURSE);
    }
}
