<?php
/**
 * Created by PhpStorm.
 * User: tu.duong
 * Date: 08/01/2019
 * Time: 02:55:47
 */

namespace App\Transformers;

use App\Models\Grammar;
use Carbon\Carbon;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class GrammarTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    //private $fractal;

    /**
     * @var GrammarTransformer
     */
    //private $grammarSentenceTransformer;

    //function __construct(Manager $fractal, GrammarSentenceTransformer $grammarSentenceTransformer)
    //{
        //$this->fractal = $fractal;
        //$this->grammarSentenceTransformer = $grammarSentenceTransformer;
    //}

    public function transform(Grammar $grammar)
    {
//        $sentences = $this->fractal->createData(new Collection($grammar->sentences, $this->grammarSentenceTransformer))->toArray()['data'];
        return [
            'id' => $grammar->id,
            'name' => $grammar->name,
            'level_id' => $grammar->level_id ? $grammar->level_id : 0,
            'level' => $grammar->level_id ? $grammar->level->name : null,
            'video' => $grammar->video ? (!filter_var($grammar->video, FILTER_VALIDATE_URL) ? media_url_web($grammar->video) : $grammar->video) : null,
            'translations' => $grammar->getTranslationsArray(),
//            'sentences' => $sentences,
            'is_active' => $grammar->is_active,
            'created_at' => Carbon::parse($grammar->created_at)->format('d-m-Y'),
            'created_by' => $grammar->user ? $grammar->user->name : null,
        ];
    }
}
