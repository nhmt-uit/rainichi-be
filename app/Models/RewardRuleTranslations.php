<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRuleTranslations extends Model
{
    public $table = 'reward_rules_translations';

    public $timestamps = false;

    public $fillable = ['description', 'method'];
}
