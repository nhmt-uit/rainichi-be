<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\CourseClass;
use App\Models\CourseTest;
use App\Models\Order;
use App\Models\PurchasedCourse;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class TestTransformer extends TransformerAbstract
{

    public function transform(Test $test)
    {
        return [
            'id' => $test->id,
            'price' => $test->price,
            'reward' => $test->reward,
            'image' => $test->image ? media_url_web($test->image) : null,
            'video' => filter_var($test->video, FILTER_VALIDATE_URL) ? $test->video : (isset($test->video) ? media_url_web($test->video) : null),
            'translations' => $test->getTranslationsArray()
        ];
    }
}
