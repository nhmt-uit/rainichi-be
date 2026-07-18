<?php
/**
 * Created by PhpStorm.
 * User: tuduong
 * Date: 08/01/2019
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Grammar;

use App\Http\Controllers\Controller;
use App\Models\Grammar;
use App\Service\BaseResponse;
use App\Transformers\GrammarTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class DetailController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var GrammarTransformer
     */
    private $grammarTransformer;

    function __construct(Manager $fractal, GrammarTransformer $grammarTransformer)
    {
        $this->fractal = $fractal;
        $this->grammarTransformer = $grammarTransformer;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        $grammar = Grammar::query()->find($id);
        if ($grammar) {
            $grammar = new Item($grammar, $this->grammarTransformer);
            $grammar = $this->fractal->createData($grammar);

            return BaseResponse::customResponse(
                'Get grammar by id successfully',
                $grammar->toArray()['data'],
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
