<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    /**
     * @var string
     */
    protected $table = 'landing_page';

    /**
     * @var array
     */
    protected $fillable = ['id', 'type', 'name', 'description', 'is_active', 'updated_by'];


    const INDEX_PAGE = 1;
    const CENTER_PAGE = 2;
    const ENTERPRISE_PAGE = 3;
}
