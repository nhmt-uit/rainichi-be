<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VocabularyTranslations extends Model
{
    public $timestamps = false;
    protected $fillable = ['chinese_vietnamese_word', 'meaning', 'example1', 'example2', 'audio_example_1', 'audio_example_2'];

    /**
     * @return string|null
     */
    public function getAudioExample1Attribute()
    {
        return isset($this->attributes['audio_example_1']) ? media_url_web($this->attributes['audio_example_1']) : null;
    }

    public function getAudioExample2Attribute()
    {
        return isset($this->attributes['audio_example_2']) ? media_url_web($this->attributes['audio_example_2']) : null;
    }
}
