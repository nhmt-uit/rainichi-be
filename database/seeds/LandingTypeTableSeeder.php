<?php

use App\Models\LandingType;
use Illuminate\Database\Seeder;

class LandingTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * composer dump-autoload
     * Run: php artisan db:seed --class=LandingTypeTableSeeder
     * @return void
     */
    public function run()
    {
        $landing_type_default = [
            [
                'type' => LandingType::STRONG_APP,
                'description' => 'Điểm mạnh của ứng dụng',
            ],
            [
                'type' => LandingType::INTRO_APP,
                'description' => 'Giới thiệu tính năng ứng dụng',
            ],
            [
                'type' => LandingType::ONLINE_COURSE,
                'description' => 'Khóa học trực tuyến',
            ],
            [
                'type' => LandingType::CENTER,
                'description' => 'Trung Tâm Tiếng Nhật',
            ],
            [
                'type' => LandingType::CUSTOMER,
                'description' => 'Người dùng',
            ],
            [
                'type' => LandingType::PARTNER,
                'description' => 'Đối tác',
            ],
            [
                'type' => LandingType::CENTER_COURSE,
                'description' => 'Khóa học tại Rainichi',
            ],
            [
                'type' => LandingType::EDUCATION,
                'description' => 'Đào tạo gì tại Rainichi',
            ],
            [
                'type' => LandingType::OPENING_COURSE,
                'description' => 'Khóa học sắp khai giảng tại Trung tâm',
            ],
            [
                'type' => LandingType::JOIN_TEAM,
                'description' => 'Gia nhập Team Rainichi',
            ],
            [
                'type' => LandingType::REGISTER_NOW,
                'description' => 'Đăng kí ngay',
            ],
        ];
        LandingType::query()->delete();
        foreach ($landing_type_default as $key => $type) {
            $type['name'] = $type['description'];
            LandingType::create($type);
        }
    }
}
