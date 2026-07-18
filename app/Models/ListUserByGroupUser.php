<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListUserByGroupUser extends Model
{
    public $timestamps = true;

    protected $table = 'list_user_by_group_user';

    protected $fillable = ['group_user_id', 'user_id'];

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchString($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->where('user_id', $search_string);
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
