<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\CourseClass;
use App\Models\Test;
use App\Models\TestResult;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class MyTestTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var TestResultTransformer
     */

    private $testResultTransformer;
    protected $user_id;


    function __construct(Manager $fractal, TestResultTransformer $testResultTransformer)
    {
        $this->fractal = $fractal;
        $this->testResultTransformer = $testResultTransformer;
    }

    public function transform(Test $test)
    {
        $result = $test->result->sortByDesc('id')->first();
        $courseValid = CourseClass::query()->availableCourse($this->user_id, null, $test->id)->first();
        $is_enterprise = $courseValid ? true : false;
        return [
            'id' => $test->id,
            'type' => $test->type,
            'score' => $result ? round($result->score) : 0,
            'total_question' => $result ? $result->total_question : 0,
            'total_score' => $result ? round($result->total_score) : 0,
            'total_time' => $result ? round($result->total_time) : 0,
            'result' => $result ? ($result->status == 0 ? "FAILED" : "PASSED") : "NONE",
            'date' => $result ? Carbon::parse($result->created_at)->format('d-m-Y') : null,
            'video' => filter_var($test->video, FILTER_VALIDATE_URL) ? $test->video : (isset($test->video) ? media_url_web($test->video) : null),
            'translations' => $test->getTranslationsArray(),
            'test_results' => $result ? $this->fractal->createData(new Collection($test->result, $this->testResultTransformer))->toArray()['data'] : [],
            'is_enterprise' => $is_enterprise
        ];
    }

    public function setUserIdParams($user_id)
    {
        $this->user_id = $user_id;
    }
}
