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

class TestAdminTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var TestTimeTransformer
     */
    private $testTimeTransformer;

    /**
     * @var ChapterTransformer
     */
    private $chapterTransformer;


    function __construct(Manager $fractal, TestTimeTransformer $testTimeTransformer, ChapterTransformer $chapterTransformer)
    {
        $this->fractal = $fractal;
        $this->testTimeTransformer = $testTimeTransformer;
        $this->chapterTransformer = $chapterTransformer;
    }

    public function transform(Test $test)
    {
        $cl = null;
        $bought = null;
        if ($test->course_id) {
            $cl = CourseTest::query()
                ->where('course_id', $test->course_id)
                ->where('test_id', $test->id)
                ->first();
        }
        if (Auth::check()) {
            $bought = PurchasedCourse::query()
                ->where('test_id', $test->id)
                ->where('user_id', Auth::user()->id)
                ->orderByDesc('id')
                ->first();

            if (empty($bought)) {
                $bought = CourseClass::query()->availableCourse(Auth::user()->id, null, $test->id)->first();
            }
        }
        return [
            'id' => $test->id,
            'name' => $test->name,
            'level_id' => $test->level_id,
            'level' => $test->level_id ? $test->levels->name : null,
            'is_active' => $test->is_active,
            'is_combine' => $test->is_combine,
            'type' => $test->type,
            'in_course' => $test->course_id ? [
                'is_active' => $cl->is_active,
                'sort_order' => $cl->sort_order,
            ] : null,
            'test_fail' => $test->testFail,
            'test_chapter' => $test->chapters ? $this->fractal->createData(new Collection($test->chapters, $this->chapterTransformer))->toArray()['data'] : null,
            'test_section' => $test->testTimes ? $this->fractal->createData(new Collection($test->testTimes, $this->testTimeTransformer))->toArray()['data'] : null,
//            'questions' => $test->getTotalQuestion(),
            'questions' => 0,
            'minimum_score' => $test->minimum_score,
            'price' => $test->price,
            'bought' => $bought ? true : ($test->price == 0 ? true : false),
            'reward' => $test->reward,
            'discount_price' => $test->discount_price,
            'enterprise_price' => $test->enterprise_price,
            'enterprise_discount_price' => $test->enterprise_discount_price,
            'customer_type_id' => $test->customer_type_id,
            'image' => $test->image ? media_url_web($test->image) : null,
            'video' => filter_var($test->video, FILTER_VALIDATE_URL) ? $test->video : (isset($test->video) ? media_url_web($test->video) : null),
            'translations' => $test->getTranslationsArray(),
            'created_at' => Carbon::parse($test->created_at)->format('d-m-Y'),
            'created_by' => $test->user ? $test->user->name : '',
            'sort_order' => $test->sort_order

        ];
    }
}
