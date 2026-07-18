<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RewardLog extends Model
{
    protected $table = 'reward_logs';

    public $timestamps = true;

    protected $fillable = ['user_id', 'key', 'credit'];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo(EventDay::class, 'event_id', 'id');
    }

    public function reward() {
        return $this->belongsTo(RewardRule::class, 'key', 'route');
    }
}
