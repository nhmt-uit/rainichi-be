<?php

namespace App\Models;

use App\Transformers\CategoryTransformer;
use Illuminate\Database\Eloquent\Model;

class Category extends Base
{
    use \Dimsav\Translatable\Translatable;

    protected $table = 'category';

    protected $fillable = ['parent_id', 'slug', 'link_to', 'sort', 'position', 'is_default', 'is_active','created_by', 'updated_by', 'type'];

//    mapping translate
    public $translatedAttributes = ['name', 'short_content', 'slug'];

    const POS_HEADER = 1;
    const POS_FOOTER = 2;
    const POS_ALL = 3;

    //Declare link_to

    const LINK_TO_NONE = 1;
    const LINK_TO_BLOG = 2;
    const LINK_TO_POST = 3;
    const LINK_TO_CONTACT = 4;
    const LINK_TO_SHOP = 5;
    const LINK_TO_EXAM = 6;
    const LINK_TO_COURSE = 7;

    //Declare type
    const NEWS = 1; // news
    const JOB = 2; // jobs
    const FAQ = 3; // question and answers


    /**
     * @return  array types that provide CMS access
     */
    public static function getCategoryPosition()
    {
        $category =[
            ['id' => Category::POS_HEADER, 'name' => 'Header'],
            ['id' => Category::POS_HEADER, 'name' => 'Footer'],
            ['id' => Category::POS_ALL, 'name' => 'All'],
        ];
        return $category;
    }

    public function articles(){
        return $this->hasMany(Article::class,'category_id','id');
    }

    public function sliders(){
        return $this->hasMany(Slider::class,'category_id','id');
    }

    public function hasChild()
    {
        $has_child = $this->children()->count() > 0 ? true : false;
        return $has_child;

    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public  function scopeType($q, $type){
        return $q->whereNotNull('type')->where('type', $type);
    }

    public function scopeGetTreeCategory($q)
    {
        $parents = $q->with('children')->isActive(true)->whereNull('parent_id')->get();
        $dataTree = Category::generateTree($parents);
        return $dataTree;
    }

    public static function generateTree($categories, $data = [])
    {
        foreach ($categories as $key => $category){
            $temp = (new CategoryTransformer)->transform($category);
            if($category->hasChild()){
                $temp['children'] = self::generateTree($category->children);
            }
            array_push( $data, $temp);
        }
        return $data;
    }

    public function scopeGetPosition($q, $arr_position)
    {
        return $q->whereIn('position', $arr_position)->isActive(true)->orderBy('sort', 'asc')->get();
    }

    /**
     * @param $q
     * @param $slug
     * @return mixed
     */
    public function scopeGetBySlug($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->WhereHas('translations',
                function ($query) use ($search_string) {
                    $query->where('slug', '=', $search_string );
                }
            );
        }
    }

}
