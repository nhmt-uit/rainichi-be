<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingContent extends Base
{
    use \Astrotomic\Translatable\Translatable;

    public $table = 'landing_content';

    public $translatedAttributes = ['title', 'sub_title', 'short_content', 'content', 'start_date',
        'time_range', 'address'];

    protected $fillable = [
        'id',
        'parent_id',
        'landing_type_id',
        'landing_page_id',
        'image',
        'url',
        'video',
        'sort',
        'is_active',
        'register_time'
    ];
    public $timestamps = true;
    protected $casts = [
        'is_active' => 'boolean',
        'register_time' => 'datetime'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function page()
    {
        return $this->belongsTo(LandingPage::class, 'landing_page_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(LandingType::class, 'landing_type_id');
    }

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchBy($q, $search_string, $lang)
    {
        if (isset($search_string)) {
            return $q->orWhereHas('translations',
                function ($query) use ($search_string, $lang) {
                    $query->where('title', 'like', '%' . $search_string . '%');
                    $query->orWhere('sub_title', 'like', '%' . $search_string . '%');
                    $query->where('locale', $lang);
                }
            );
        }
    }

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @return mixed
     */
    public function scopeOrderByCustom($q, $column, $order_by_type)
    {
        if (isset($column, $order_by_type)) {
            if ($column !== 'title') {
                return $q->orderBy($column, $order_by_type);
            }
        } else {
            return $q->orderBy('updated_at', 'desc');
        }
    }

    /**
     * @param $q
     * @param $type_id
     * @return mixed
     */
    public function scopeType($q, $type_id)
    {
        if (isset($type_id)) {
            return $q->where('landing_type_id', $type_id);
        }
    }

    /**
     * @param $q
     * @param $page_id
     * @return mixed
     */
    public function scopePage($q, $page_id)
    {
        if (isset($page_id)) {
            return $q->where('landing_page_id', $page_id);
        }
    }
}
