<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DefaultAvatar;
use App\Models\User;

class DefaultAvatarTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Run: php artisan db:seed --class=DefaultAvatarTableSeeder
     * @return void
     */
    public function run()
    {
        $creator = User::query()->where('type', User::ADMIN)->first();
        if (!$creator) {
            $creator = User::query()->first();
        }
        if (!$creator) {
            return;
        }

        $avatars = [
            '4JJ7h4veZgr2C93ZSqIOq1WQN5XGNUge2rAwc9lY.png',
            'CvJq1xBC0puWS0W2XAUBGJczac9L2N9DV0aDGdun.png',
            'CvvHnML3zlVlgMCoe32Zh9BJxsubQ2ArCjPumVH1.png',
            'FEqP8E6HTaDTvhmJ7Tl5yDS5UXzdfBztWgNEjfUt.png',
            'LqxspOIZ75bu7pQnk4tge74bPgXvZH2nrbA6623S.png',
            'S4S6XJV3dwuWuAMUOq9ZLc7TGf4R1sMtUyyWWqzR.png',
            'Ul0AVPLzqf87EjXuA2bzb4cbgcoc0in5EfPxQeOj.png',
            'b3fBzsg41jFeDxsXbwn6TLz2X2X8tjSYYKCQezqi.png',
            'dhsUJ7ynvgmwJSbX4AatX9sVVgCPjTeA8ZQPP5re.png',
            'zPwcpQomtK80s79nG6uCshfwaLJjb3292yKwKypb.png',
        ];

        foreach ($avatars as $file) {
            DefaultAvatar::query()->create([
                'avatar' => 'users/default-avatars/' . $file,
                'created_by' => $creator->id,
            ]);
        }
    }
}
