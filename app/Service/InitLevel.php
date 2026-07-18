<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 4/18/19
 * Time: 15:29
 */

namespace App\Service;


use App\Models\Level;

class InitLevel
{
    /**
     * Get foundation level
     * @return mixed|null
     */
    public static function init()
    {
        $level = Level::query()->where('is_foundation', 1)->first();
        if ($level) {
            return $level->id;
        } else {
            return null;
        }
    }
}