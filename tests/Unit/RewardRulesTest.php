<?php

namespace Tests\Unit;

use App\Models\RewardRule;
use App\Models\User;
use App\Service\RewardRules;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RewardRulesTest extends TestCase
{
    use DatabaseTransactions;

    public function test_add_does_nothing_when_no_matching_reward_rule_exists()
    {
        $user = factory(User::class)->create(['credits' => 0]);

        (new RewardRules($user, 'no_such_key'))->add();

        $this->assertDatabaseMissing('reward_logs', ['user_id' => $user->id]);
        $this->assertEquals(0, $user->fresh()->credits);
    }

    public function test_add_credits_user_and_logs_when_reward_rule_matches()
    {
        Queue::fake();
        $user = factory(User::class)->create(['credits' => 0]);
        RewardRule::query()->create([
            'description' => 'Test reward',
            'credit' => 15,
            'route' => 'test_key',
            'is_active' => true,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addDay(),
        ]);

        (new RewardRules($user, 'test_key'))->add();

        $this->assertEquals(15, $user->fresh()->credits);
        $this->assertDatabaseHas('reward_logs', [
            'user_id' => $user->id,
            'credit' => 15,
            'key' => 'test_key',
        ]);
    }
}
