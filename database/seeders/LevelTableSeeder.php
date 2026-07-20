<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Run: php artisan db:seed --class=LevelTableSeeder
     * @return void
     */
    public function run()
    {
        $levels = [
            ['is_foundation' => false, 'next_level' => null, 'vi' => 'N1', 'en' => 'N1'],
            ['is_foundation' => false, 'next_level' => null, 'vi' => 'N2', 'en' => 'N2'],
            ['is_foundation' => false, 'next_level' => null, 'vi' => 'N3', 'en' => 'N3'],
            ['is_foundation' => false, 'next_level' => null, 'vi' => 'N4', 'en' => 'N4'],
            ['is_foundation' => false, 'next_level' => null, 'vi' => 'N5', 'en' => 'N5'],
            ['is_foundation' => true, 'next_level' => null, 'vi' => 'Sơ cấp', 'en' => 'Basic level'],
        ];

        foreach ($levels as $data) {
            $level = new Level([
                'is_foundation' => $data['is_foundation'],
                'next_level' => $data['next_level'],
            ]);
            $level->translateOrNew('vi')->name = $data['vi'];
            $level->translateOrNew('en')->name = $data['en'];
            $level->save();
        }
    }
}
