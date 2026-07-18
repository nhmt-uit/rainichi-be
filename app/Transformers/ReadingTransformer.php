<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Reading;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ReadingTransformer extends TransformerAbstract
{

    public function transform(Reading $reading)
    {
        return [
            'id' => $reading->id,
            'name' => $reading->name,
            'paragraph' => $reading->paragraph,
            'translations' => $reading->getTranslationsArray(),
            'is_active' => $reading->is_active,
            'questions' => $reading->questions,
            'level_id' => $reading->level_id,
            'level' => $reading->level_id ? $reading->levels->name : null,
            'created_at' => Carbon::parse($reading->created_at)->format('d-m-Y'),
            'created_by' => $reading->user ? $reading->user->name : null,
        ];
    }
}
