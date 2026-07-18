<?php
/**
 * Created by PhpStorm.
 * User: tuduong
 * Date: 08/01/2019
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Alphabet;

use App\Http\Controllers\Controller;
use App\Models\Alphabet;
use App\Models\Grammar;
use App\Service\BaseResponse;
use App\Transformers\AlphabetTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class DetailController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var AlphabetTransformer
     */
    private $alphabetTransformer;

    function __construct(Manager $fractal, AlphabetTransformer $alphabetTransformer)
    {
        $this->fractal = $fractal;
        $this->alphabetTransformer = $alphabetTransformer;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function detail($type, $id)
    {
        $alphabet = Alphabet::query()
            ->where('id', $id)
            ->where('type', Alphabet::TYPES[$type])
            ->first();

        if ($alphabet) {
            $alphabet = new Item($alphabet, $this->alphabetTransformer);
            $alphabet = $this->fractal->createData($alphabet);

            return BaseResponse::customResponse(
                'Get ' . $type . ' by id successfully',
                $alphabet->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        }

        return BaseResponse::customResponse(
            'Not found',
            [],
            false,
            404,
            404,
            'NotFound'
        );
    }
}
