<?php

use Illuminate\Database\Seeder;
use App\Models\CategoryTranslations;
class CategoryConvertSeed extends Seeder
{
    /**
     * Run the database seeds.
     * create: php artisan make:seed CategoryConvertSeed
     * Run: php artisan db:seed --class=CategoryConvertSeed
     * @return void
     */
    public function run()
    {
        $translators = CategoryTranslations::query()->get()->all();
        foreach ($translators as $key  => $cat)
        {
            $cat->slug = str_slug($cat->name, '-');
            $cat->save();
        }
    }
}
