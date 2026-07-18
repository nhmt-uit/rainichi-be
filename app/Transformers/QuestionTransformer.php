<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Question;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class QuestionTransformer extends TransformerAbstract
{
    public function transform(Question $question)
    {
        return [
            'id' => $question->id,
            'name' => $question->name,
            'translations' => $question->getTranslationsArray(),
            'question' => $question->question,
            'paragraph' => $question->paragraph,
            'image' => $question->image ,
            'media' => $question->media,
            'answer' => $question->answer,
            'media_description' => $question->media_description,
            'type' => $question->type,
            'is_skill' => $question->is_skill,
            'category' => $question->category,
            'is_active' => $question->is_active,
            'level_id' => $question->level_id ? $question->level_id : 0,
            'level' => $question->levels ? $question->levels->name : null,
            'chapter_id' => $question->chapter_id,
            'chapter' => $question->chapter ? $question->chapter : null,
            'child_questions' => $question->child_questions,
            'score' => $question->score,
            'created_at' => Carbon::parse($question->created_at)->format('d-m-Y'),
            'created_by' => $question->user ? $question->user->name : null,
            'total_question' => $question->total_question
        ];
    }
}
