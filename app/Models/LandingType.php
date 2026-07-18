<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingType extends Model
{
    /**
     * @var string
     */
    protected $table = 'landing_type';

    /**
     * @var array
     */
    protected $fillable = ['id', 'type', 'name', 'description', 'is_active', 'updated_by'];

    const STRONG_APP = 1;
    const INTRO_APP = 2;
    const ONLINE_COURSE = 3;
    const CENTER = 4;
    const CUSTOMER = 5;
    const PARTNER = 6;
    const CENTER_COURSE = 7;
    const OPENING_COURSE = 8;
    const EDUCATION = 9;
    const JOIN_TEAM = 10;
    const REGISTER_NOW = 11;

}
