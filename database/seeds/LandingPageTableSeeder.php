<?php

use Illuminate\Database\Seeder;
use App\Models\LandingPage;
class LandingPageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * composer dump-autoload
     * Run: php artisan db:seed --class=LandingPageTableSeeder
     * @return void
     */
    public function run()
    {
        $landing_page_default = [
            [
                'type' => LandingPage::INDEX_PAGE,
                'name' => 'Trang Chủ',
                'description' => 'Trang Chủ',
            ],
            [
                'type' => LandingPage::CENTER_PAGE,
                'name' => 'Trang Trung tâm',
                'description' => 'Trang Trung tâm',
            ],
            [
                'type' => LandingPage::ENTERPRISE_PAGE,
                'name' => 'Trang Doanh nghiệp',
                'description' => 'Trang Doanh nghiệp',
            ]
        ];
        LandingPage::query()->delete();
        foreach ($landing_page_default as $key => $page) {
            LandingPage::create($page);
        }
}
}
