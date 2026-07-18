<?php

namespace App\Service;


use App\Models\Chapter;

class TestService
{
    /**
     * Generate name or image from data
     * @param $chapter_id
     * @param $lang
     * @param $type
     * @return string|null
     */
    public static function generateNameOrImage($chapter_id, $lang, $type)
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
                        $image = $chapters[$id] ? media_url_web($chapters[$id]) : null;
                    }
                }
            }

            return $image;
        }


    }
}
