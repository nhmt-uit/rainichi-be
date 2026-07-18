<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Chapter;
use App\Models\TestTimes;
use League\Fractal\TransformerAbstract;

class TestTimeTransformer extends TransformerAbstract
{


    public function transform(TestTimes $testTimes)
    {
        $chapter_id = explode(',', $testTimes->chapter_id);
        return [
            'id' => $testTimes->id,
            'time' => $testTimes->time,
            'chapter_id' => $testTimes->chapter_id,
            'questions' => $testTimes->questions,
            'image' => $testTimes->chapter_id ? self::generateNameOrImage($chapter_id, 'vi', 'IMAGE') : null,
            'translations' => [
                'vi' => $testTimes->chapter_id ? ['name' => self::generateNameOrImage($chapter_id, 'vi', 'NAME')] : null,
                'en' => $testTimes->chapter_id ? ['name' => self::generateNameOrImage($chapter_id, 'en', 'NAME')] : null
            ],
            'has_children' => count($chapter_id) > 1 ? true : false
        ];
    }

    /**
     * Generate name or image from data
     * @param $chapter_id
     * @param $lang
     * @param $type
     * @return string|null
     */
    public function generateNameOrImage($chapter_id, $lang, $type)
    {
        if ($type === 'NAME') {
            $name = null;

            $chapters = Chapter::query()->join('chapter_translations as t', function ($q) use ($lang) {
                $q->on('chapter.id', '=', 't.chapter_id')->where('t.locale', '=', $lang);
            })->where('category', Chapter::EXAM)->pluck('t.name', 'chapter.id');
            if ($chapters) {
                foreach ($chapter_id as $key => $id) {

                    if ($key == 0) {
                        $name .= $chapters[$id];
                    } else {
                        $name .= ' - ' . $chapters[$id];
                    }
                }
            }
            return $name;
        } else {
            $image = null;
            $chapters = Chapter::query()->where('category', Chapter::EXAM)->pluck('image', 'id');
            if ($chapters) {
                foreach ($chapter_id as $key => $id) {

                    if ($key == 0) {
                        $image = $chapters[$id] ? media_url_web( $chapters[$id]) : null;
                    }
                }
            }

            return $image;
        }


    }
}
