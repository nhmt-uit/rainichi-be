<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\PromotionTranslations;

class PromotionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * create: php artisan make:seed PromotionTableSeeder
     * Run: php artisan db:seed --class=PromotionTableSeeder
     * @return void
     */
    public function run()
    {
        $promotion_default = [
            [
                'translations' => [
                    'vi' => [
                        'name' => 'Video hướng dẫn cách học',
                    ],
                    'en' => [
                        'name' => 'Video hướng dẫn cách học',
                    ]
                ]
            ],
            [
                'translations' => [
                    'vi' => [
                        'name' => 'Tặng bộ sách 300.000 đ',
                    ],
                    'en' => [
                        'name' => 'Tặng bộ sách 300.000 đ',
                    ]
                ]
            ],
        ];
        Promotion::query()->delete();
        foreach ($promotion_default as $key => $promotion) {
            $promo = Promotion::create(['is_active' => 1]);
            $language_keys = array_keys($promotion['translations']);
            foreach ($language_keys as $language) {
                $promo->translateOrNew($language)->name = $promotion['translations'][$language]['name'];
            }
            $promo->save();
        }
    }
}
