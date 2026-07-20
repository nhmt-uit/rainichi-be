<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CoursePrice;
class CoursePriceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * composer dump-autoload
     * Run: php artisan db:seed --class=CoursePriceTableSeeder
     * @return void
     */
    public function run()
    {
        $coursePrice = CoursePrice::query()->get();
        foreach($coursePrice as $key => $price)     
        {
            $price->update(['customer_type_id' => CoursePrice::CUSTOMER_PERSONAL]);
        }
    }
}
