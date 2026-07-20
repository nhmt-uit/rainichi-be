<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Configuration;

class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * php artisan db:seed --class=ConfigurationTableSeeder
     */
    public function run()
    {
        $configuration_data = [
            'is_active' => 1,
            'email' => 'info@vietlabo.com',
            'phone' => '(028) - 3503 4470',
            'facebook' => 'https://www.facebook.com/',
            'google_plus' => '',
            'twitter' => 'https://twitter.com/',
            'app_store_link' => 'https://apps.apple.com/us/app/rainichi/id1472177022',
            'play_store_link' => 'https://play.google.com/store/apps/details?id=com.rainichi',
            'max_day_class' => '30',

        ];
        $language_data = [
            'translations' => [
                'vi' => [
                    'title' => 'Rainichi',
                    'slogan' => 'HỌC TIẾNG NHẬT TRỰC TUYẾN - MỌI LÚC - MỌI NƠI - MỌI CHỖ TRÊN MỌI THIẾT BỊ',
                    'keywords' => 'Rainichi, học tiếng nhât cấp tốc, n1, n2, n3, n4, n5',
                    'description' => 'Website dành cho người học Tiếng Nhật online',
                    'address' => '780/14E, Sư Vạn Hạnh, Phường 12, Quận 10, TP. Hồ Chí Minh',
                ],
                'en' =>  [
                    'title' => 'Rainichi',
                    'slogan' => 'HỌC TIẾNG NHẬT TRỰC TUYẾN - MỌI LÚC - MỌI NƠI - MỌI CHỖ TRÊN MỌI THIẾT BỊ',
                    'keywords' => 'Rainichi, học tiếng nhât cấp tốc, n1, n2, n3, n4, n5',
                    'description' => 'Website dành cho người học Tiếng Nhật online',
                    'address' => '780/14E, Sư Vạn Hạnh, Phường 12, Quận 10, TP. Hồ Chí Minh',
                ],
            ]
        ];
        Configuration::query()->delete();
        $config = Configuration::create($configuration_data);
        $language_keys = array_keys($language_data['translations']);
        foreach ($language_keys as $language) {
            $config->translateOrNew($language)->title = $language_data['translations'][$language]['title'];
            $config->translateOrNew($language)->slogan = $language_data['translations'][$language]['slogan'];
            $config->translateOrNew($language)->keywords = $language_data['translations'][$language]['keywords'];
            $config->translateOrNew($language)->description = $language_data['translations'][$language]['description'];
            $config->translateOrNew($language)->address = $language_data['translations'][$language]['address'];
        }
        $config->save();
    }
}
