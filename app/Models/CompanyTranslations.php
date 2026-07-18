<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyTranslations extends Base
{

    protected $table = 'company_translations';
    protected $fillable = ['name', 'career', 'content', 'company_id', 'locale'];

    public $timestamps = false;

}
