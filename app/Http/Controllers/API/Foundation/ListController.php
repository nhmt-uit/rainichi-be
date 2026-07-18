<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Foundation;


use App\Http\Controllers\Controller;
use App\Models\Alphabet;
use App\Models\Foundation;
use App\Models\Number;
use App\Service\BaseResponse;
use App\Transformers\AlphabetTransformer;
use App\Transformers\FoundationTransformer;
use App\Transformers\NumberTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var AlphabetTransformer
     */
    private $alphabetTransformer;
    /**
     * @var NumberTransformer
     */
    private $numberTransformer;

    /**
     * @var FoundationTransformer
     */
    private $foundationTransformer;

    function __construct(Manager $fractal, AlphabetTransformer $alphabetTransformer, NumberTransformer $numberTransformer, FoundationTransformer $foundationTransformer)
    {
        $this->fractal = $fractal;
        $this->alphabetTransformer = $alphabetTransformer;
        $this->numberTransformer = $numberTransformer;
        $this->foundationTransformer = $foundationTransformer;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $alphabet = Alphabet::getByType($request->query('type'))->get();
        $alphabet = new Collection($alphabet, $this->alphabetTransformer);
        $alphabet = $this->fractal->createData($alphabet);
        return BaseResponse::customResponse(
            'Get list alphabet successfully',
            $alphabet->toArray()['data'],
            true,
            200, 200,
            'Success'
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function listNumber()
    {
        $number = Number::all();
        $number = new Collection($number, $this->numberTransformer);
        $number = $this->fractal->createData($number);
        return BaseResponse::customResponse(
            'Get list number successfully',
            $number->toArray()['data'],
            true,
            200, 200,
            'Success'
        );
    }

    public function listFoundation(Request $request)
    {
        $foundation = Foundation::getByType($request->query('type'))->get();
        $foundation = new Collection($foundation, $this->foundationTransformer);
        $foundation = $this->fractal->createData($foundation);
        return BaseResponse::customResponse(
            'Get list number successfully',
            $foundation->toArray()['data'],
            true,
            200, 200,
            'Success'
        );
    }
}
