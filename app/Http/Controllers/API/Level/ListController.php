<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 17/12/2018
 * Time: 10:50
 */

namespace App\Http\Controllers\API\Level;


use App\Http\Controllers\Controller;
use App\Models\Level;
use App\Service\BaseResponse;
use App\Transformers\LevelTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var LevelTransformer
     */
    private $levelTransformer;

    function __construct(Manager $fractal, LevelTransformer $levelTransformer)
    {
        $this->fractal = $fractal;
        $this->levelTransformer = $levelTransformer;
    }
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $levels = Level::all();
        $levels = new Collection($levels, $this->levelTransformer);
        $levels = $this->fractal->createData($levels);
        return BaseResponse::customResponse(
            'Success',
            $levels->toArray()['data'],
            true,
            200, 200,
            'Success'
        );
    }
}
