<?php

use Illuminate\Database\Seeder;
use App\Models\CoursePriceCurrency;
use App\Models\CoursePrice;
class CoursePriceCurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * composer dump-autoload
     * Run: php artisan db:seed --class=CoursePriceCurrencyTableSeeder
     * @return void
     */
    public function run()
    {
        $currencies = [
            [
                'currency' => 'XU',
            ],
            [
                'currency' => 'VND',
            ]
        ];

        foreach ($currencies as $val) {
            CoursePriceCurrency::create($val);
        }

        #update course price
        $coursePrice = CoursePrice::query()->get();
        foreach ($coursePrice as $key => $c_p)
        {
            $c_p->update(['course_price_currency_id' => 1]);
        }

    }
}
