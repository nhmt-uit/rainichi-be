<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\CourseType;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class CourseTypeAdminTransformer extends TransformerAbstract
{

    public function transform(CourseType $courseType)
    {
        return [
            'id' => $courseType->id,
            'has_course_children' => $courseType->has_course_children,
            'name' => $courseType->name,
            'translations' => $courseType->getTranslationsArray(),
            'created_at' => Carbon::parse($courseType->created_at)->format('d-m-Y'),
            'created_by' => $courseType->createdBy->name,
            'is_active' => $courseType->is_active,
            'is_delete' => $courseType->is_delete
        ];
    }
}
