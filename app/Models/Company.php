<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Base
{
    use SoftDeletes, \Astrotomic\Translatable\Translatable;


    protected $table = 'companies';
    protected $fillable = ['type', 'name', 'image', 'phone', 'fax', 'email', 'career', 'content', 'charter_capital',
        'num_of_employee', 'is_active', 'sort_order', 'created_by', 'updated_by'
    ];

    public $translatedAttributes = ['name', 'career', 'content'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */

    public function classRoom()
    {
        return $this->hasMany(Classroom::class, 'company_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongstoMany(User::class, 'user_company', 'company_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @param $user_id
     * @return Builder|Model|object|null
     */
    public static function getCompanyByUser($user_id)
    {
        return Company::query()
            ->join('user_company', 'user_company.company_id', '=', 'companies.id')
            ->where('user_company.user_id', $user_id)
            ->where('user_company.is_active', true)
            ->whereNull('companies.deleted_at')
            ->where('companies.is_active', true)
            ->select('companies.*')->first();
    }

    /**
     * @param $q
     * @param $search_string
     */
    public function scopeSearchName($q, $search_string)
    {
        if ($search_string) {
            $q->where('name', 'like', '%' . $search_string . '%');
        }
    }

}
