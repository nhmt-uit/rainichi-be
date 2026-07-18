<?php

namespace App\Http\Controllers\API\Alphabet;

use App\Models\Alphabet;
use App\Transformers\AlphabetTransformer;
use App\Http\Controllers\Controller;
use League\Fractal\Resource\Collection;
use League\Fractal\Manager;
use App\Service\BaseResponse;


class ListController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
    /**
     * @var AlphabetTransformer
     */
    private $alphabetTransformer;

    function __construct(Manager $fractal, AlphabetTransformer $alphabetTransformer) {
        $this->fractal = $fractal;
        $this->alphabetTransformer = $alphabetTransformer;
    }

    /**
     * @param $type
     * @return mixed
     */
    public function index($type)
    {

        $alphabet = Alphabet::query()->where('type', Alphabet::TYPES[$type])->get();

        $alphabet = new Collection($alphabet, $this->alphabetTransformer);
        $alphabet = $this->fractal->createData($alphabet)->toArray()['data'];

        return BaseResponse::customResponse(
            'Get list ' . $type . ' successfully',
            $alphabet,
            true,
            200, 200,
            'Success'
        );
    }
}
