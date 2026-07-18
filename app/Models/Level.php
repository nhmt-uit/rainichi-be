<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use \Dimsav\Translatable\Translatable;
    protected $table = 'level';

    public $translatedAttributes = ['name'];

    protected $casts = [
        'is_foundation' => 'boolean'
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vocabularies()
    {
        return $this->belongsToMany('App\Models\Vocabulary', 'vocabulary_level', 'level_id', 'vocabulary_id');
    }
}
