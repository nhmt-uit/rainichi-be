<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public $table = 'permission';

    protected $fillable = [
        'url',
        'fe_url',
        'name',
        'parent_id',
        'free_access',
        'method'
    ];

    public $casts = [
        'free_access' => 'boolean'
    ];

    public $timestamps = true;


}
