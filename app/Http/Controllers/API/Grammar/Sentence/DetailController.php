<?php
/**
 * Created by PhpStorm.
 * User: tuduong
 * Date: 08/01/2019
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Grammar\Sentence;

use App\Http\Controllers\Controller;
use App\Models\GrammarSentence;
use App\Service\BaseResponse;
use App\Transformers\GrammarSentenceTransformer;
use App\Transformers\GrammarTransformer;
use Illuminate\Http\Request;
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
    private $grammarSentenceTransformer;

    function __construct(Manager $fractal, GrammarSentenceTransformer $grammarSentenceTransformer)
    {
        $this->fractal = $fractal;
        $this->grammarSentenceTransformer = $grammarSentenceTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        $grammar_id = $request->route('grammar_id');
        $id = $request->route('id');
        $sentence = GrammarSentence::query()
            ->where('id', $id)
            ->where('grammar_id', $grammar_id)
            ->first();

        if ($sentence) {
            $sentence = new Item($sentence, $this->grammarSentenceTransformer);
            $sentence = $this->fractal->createData($sentence);

            return BaseResponse::customResponse(
                'Get sentence by id successfully',
                $sentence->toArray()['data'],
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
