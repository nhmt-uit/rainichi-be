<?php

namespace Tests\Feature;

use App\Models\DefaultAvatar;
use App\Models\Level;
use App\Models\RewardRule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\ClientRepository;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    private $envBackup = [];

    protected function tearDown(): void
    {
        foreach ($this->envBackup as $key => $value) {
            $value === null ? putenv($key) : putenv("$key=$value");
        }
        parent::tearDown();
    }

    /**
     * Create a real password-grant Passport client and point the app's
     * CLIENT_ID/CLIENT_PASSWORD/GRANT_TYPE env vars at it for this test,
     * since AuthController reads those directly via env().
     */
    protected function usePasswordGrantClient()
    {
        $client = app(ClientRepository::class)->createPasswordGrantClient(
            null, 'Test Password Grant Client', 'http://localhost'
        );

        foreach (['CLIENT_ID', 'CLIENT_PASSWORD', 'GRANT_TYPE'] as $key) {
            $this->envBackup[$key] = getenv($key);
        }
        putenv('CLIENT_ID=' . $client->id);
        putenv('CLIENT_PASSWORD=' . $client->secret);
        putenv('GRANT_TYPE=password');
    }

    protected function seedActivationPrerequisites()
    {
        Level::query()->forceCreate(['is_foundation' => 1]);
        $creator = User::factory()->create();
        DefaultAvatar::query()->forceCreate([
            'avatar' => 'users/default-avatars/default.png',
            'created_by' => $creator->id,
        ]);
    }

    protected function seedSignUpRewardRule()
    {
        RewardRule::query()->create([
            'description' => 'Sign up reward',
            'credit' => 10,
            'route' => config('reward_constant.sign_up'),
            'is_active' => true,
            'start_date' => Carbon::now()->subDay(),
            'end_date' => Carbon::now()->addYear(),
        ]);
    }

    public function test_sign_up_creates_inactive_user_and_sends_activation_mail()
    {
        Mail::fake();

        $response = $this->postJson('/api/auth/signup', [
            'email' => 'new-user@example.com',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'new-user@example.com',
            'active' => 0,
        ]);
        Mail::assertSent(\App\Mail\RegisterMail::class);
    }

    public function test_sign_up_rejects_already_active_email()
    {
        $user = User::factory()->create([
            'email' => 'taken@example.com',
            'active' => true,
        ]);

        $response = $this->postJson('/api/auth/signup', [
            'email' => $user->email,
        ]);

        $response->assertStatus(422);
    }

    public function test_signup_activate_sets_password_and_returns_token()
    {
        $this->seedActivationPrerequisites();
        $this->seedSignUpRewardRule();
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'activate-me@example.com',
            'active' => false,
            'password' => null,
            'activation_token' => '123456',
        ]);

        $response = $this->patchJson('/api/auth/signup/activate', [
            'email' => $user->email,
            'activation_token' => '123456',
            'name' => 'Test User',
            'phone' => '0900000000',
            'password' => 'Password1',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['access_token', 'token_type', 'profile'],
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'activate-me@example.com',
            'active' => 1,
            'name' => 'Test User',
        ]);
    }

    public function test_signup_activate_rejects_invalid_token()
    {
        $user = User::factory()->create([
            'email' => 'wrong-token@example.com',
            'active' => false,
        ]);

        $response = $this->patchJson('/api/auth/signup/activate', [
            'email' => $user->email,
            'activation_token' => 'does-not-match',
            'name' => 'Test User',
            'phone' => '0900000000',
            'password' => 'Password1',
        ]);

        $response->assertStatus(404);
    }

    public function test_login_succeeds_and_returns_access_and_refresh_tokens()
    {
        $this->usePasswordGrantClient();
        $user = User::factory()->create([
            'email' => 'login-success@example.com',
            'active' => true,
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login-success@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['access_token', 'refresh_token', 'token_type', 'profile'],
        ]);
        $this->assertEquals($user->id, $response->json('data.profile.id'));
    }

    public function test_login_rejects_wrong_password()
    {
        User::factory()->create([
            'email' => 'login-user@example.com',
            'active' => true,
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'login-user@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_rejects_inactive_user()
    {
        User::factory()->create([
            'email' => 'inactive-user@example.com',
            'active' => false,
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive-user@example.com',
            'password' => 'correct-password',
        ]);

        $response->assertStatus(401);
    }

    public function test_verify_email_exists()
    {
        $user = User::factory()->create(['email' => 'exists@example.com']);

        $response = $this->postJson('/api/auth/signup/verify-email', ['email' => $user->email]);
        $response->assertJson(['exists' => true]);

        $response = $this->postJson('/api/auth/signup/verify-email', ['email' => 'nobody@example.com']);
        $response->assertJson(['exists' => false]);
    }
}
