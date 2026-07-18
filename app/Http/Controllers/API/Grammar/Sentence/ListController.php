<?php
/**
 * Created by PhpStorm.
 * User: tuduong
 * Date: 08/01/2019
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Grammar\Sentence;

use App\Models\GrammarSentence;
use App\Transformers\GrammarSentenceTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\BaseResponse;
use App\Transformers\GrammarTransformer;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
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
     * List all grammar sentences
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $grammar_id = $request->route('grammar_id');
        $paging = $request->query('limit') ? $request->query('limit') : 15;
        $sentence_list = GrammarSentence::where('grammar_id', $grammar_id)->orderByDesc('updated_at')->paginate($paging);

        $sentences = new Collection($sentence_list->items(), $this->grammarSentenceTransformer);
        $sentences->setPaginator(new IlluminatePaginatorAdapter($sentence_list));
        $sentences = $this->fractal->createData($sentences);

        return BaseResponse::customResponse(
            'Get list successfully',
            $sentences->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $sentences->toArray()['meta']
        );
    }
}
