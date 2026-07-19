<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Base
{
    use \Astrotomic\Translatable\Translatable;

    protected $table = 'user_info';
    protected $fillable = ['user_id', 'country_id', 'facebook', 'google_plus', 'twitter', 'instagram'];

    //    mapping translate
    public $translatedAttributes = ['content', 'diploma', 'major'];


    public function scopeCreateOrUpdate($q, $data, $user_id) {
        $record = $q->where('user_id', $user_id)->first();
        if (is_null($record)) {
            $data['user_id'] = $user_id;
            return self::create($data);
        } else {
            $record->update($data);
            return $record;
        }
    }
    
}
