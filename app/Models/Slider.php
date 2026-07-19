<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Base
{
    use \Astrotomic\Translatable\Translatable;
    protected $table = 'sliders';
    public $translatedAttributes = ['title', 'content'];

    protected $fillable = [ 'image', 'sort_order', 'link_to', 'link_to_type', 'type', 'platform',
                            'is_active', 'created_by', 'updated_by', 'category_id' ];

    //Declare type
    const SLIDER = 1; // SLIDER
    const GALLERY = 2; //GALLERY

    //Declare platform

    const WEBSITE = 1; // website
    const MOBILE = 2; // mobile

    // Declare link_to_type

    const NONE_LINK = 0; // none link to other
    const OPEN_INSIDE = 1; // inside
    const OPEN_OUTSIDE = 2; // outside


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

    public static function allowTypeAccess(){
        return [
            Slider::SLIDER,
            Slider::GALLERY,
        ];
    }

    public static function allowPlatformAccess(){
        return [
            Slider::WEBSITE,
            Slider::MOBILE,
        ];
    }

    public function scopeType($q, $type)
    {
        if (isset($type) ) {
            return $q->where('type', (int)$type);
        }
    }

    public function scopePlatform($q, $platform)
    {
        if (isset($platform) ) {
            return $q->where('platform', (int)$platform);
        }
    }

    public function scopeLinkToType($q, $link_to_type)
    {
        if (isset($link_to_type) ) {
            return $q->where('link_to_type', (int)$link_to_type);
        }
    }

    /**
     * @param $q
     * @param $search_string
     * @return mixed
     */
    public function scopeSearchBy($q, $search_string, $lang)
    {
        if (isset($search_string)) {
            return $q->orWhereHas('translations',
                function ($query) use ($search_string, $lang) {
                    $query->where('title', 'like', '%' . $search_string . '%');
                    $query->where('locale', $lang);
                }
            );
        }
    }

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @param $lang
     * @return mixed
     */
    public function scopeOrderByName($q, $column, $order_by_type, $lang)
    {
        if (isset($column) && isset($order_by_type) && isset($lang) && $column === 'title') {
            return $q->join('sliders_translations as t', function ($join) use ($lang) {
                $join->on('sliders.id', '=', 't.slider_id')
                    ->where('t.locale', '=', $lang);
            })->groupBy('sliders.id')
                ->orderBy('t.title', $order_by_type)->select('sliders.*');

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



}
