<?php

use Illuminate\Database\Seeder;
use App\Models\Country;

class CountryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * composer dump-autoload
     * Run: php artisan db:seed --class=CountryTableSeeder
     * @return void
     */
    public function run()
    {
        $countries = [
            [
                'code' => 'VN',
                'translations' => [
                    'vi' => [
                        'name' => 'Việt Nam',
                    ],
                    'en' => [
                        'name' => 'Vietnamese'
                    ]
                ]
            ],
            [
                'code' => 'JP',
                'translations' => [
                    'vi' => [
                        'name' => 'Nhật Bản',
                    ],
                    'en' => [
                        'name' => 'Japanese'
                    ]
                ]
            ],
            [
                'code' => 'GB',
                'translations' => [
                    'vi' => [
                        'name' => 'Anh',
                    ],
                    'en' => [
                        'name' => 'United Kingdom'
                    ]
                ]
            ],
            [
                'code' => 'US',
                'translations' => [
                    'vi' => [
                        'name' => 'Mỹ',
                    ],
                    'en' => [
                        'name' => 'United State'
                    ]
                ]
            ]
        ];

        foreach ($countries as $key => $cou_data){
            $new_data = [
                'code' => $cou_data['code'],
                'is_active' => 1
            ];
            $coun = Country::create($new_data);
            $language_keys = array_keys($cou_data['translations']);
            foreach ($language_keys as $language) {
                $coun->translateOrNew($language)->name = $cou_data['translations'][$language]['name'];
            }
            $coun->save();
        }

        $language = \App\Models\Language::query()->get();
        foreach ($language as $lang)
        {
            $country_code = $lang->code == 'vi' ?  'VN' : 'GB';
            $country = Country::query()->where('code', $country_code)->first();
            $lang->country_id = $country->id;
            $lang->save();
        }
    }
}
