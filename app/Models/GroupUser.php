<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    public $timestamps = true;

    protected $table = 'group_user';

    protected $fillable = ['name', 'created_by', 'updated_by', 'status'];

    protected $casts = ['status' => 'boolean'];

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchString($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->where('name', 'like', '%' . $search_string . '%');
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }
}
