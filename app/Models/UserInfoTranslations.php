<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfoTranslations extends Model
{

    public $timestamps = false;
    protected $table = 'user_info_translations';
    protected $fillable = ['user_info_id', 'content', 'diploma', 'major'];
    protected $touches = ['userInfo'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class);
    }

}
