<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//         User seeder.
        \App\Models\User::create([
            "name" => "Rai.Nichi",
            "email" => "admin@rai.nichi.com",
            "password" => bcrypt("123456"),
            "phone" => "098868654",
            "avatar" => "users/avatars/avatar-default.png",
            "type" => \App\Models\User::ADMIN,
            "active" => 1,
            'activation_token' => '123456',
            'email_verified_at' => \Carbon\Carbon::now(),
            "created_at" => \Carbon\Carbon::now()
        ]);
        \App\Models\Level::query()->create([
            'name' => 'N5'
        ]);

        // Level seeder
    }
}
