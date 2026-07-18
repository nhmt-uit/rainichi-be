<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\KanjiGroup;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class KanjiGroupTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var KanjiAdminTransformer
     */
    private $kanjiAdminTransformer;

    function __construct(Manager $fractal, KanjiAdminTransformer $kanjiAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->kanjiAdminTransformer = $kanjiAdminTransformer;
    }
    public function transform(KanjiGroup $kanjiGroup)
    {
        return [
            'id' => $kanjiGroup->id,
            'group_chapter_id' => $kanjiGroup->group_chapter_id,
            'kanji' => $kanjiGroup->kanji ? $this->fractal->createData(new Item($kanjiGroup->kanji, $this->kanjiAdminTransformer))->toArray()['data'] : null
        ];
    }
}
