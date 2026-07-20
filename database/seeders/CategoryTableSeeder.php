<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;


class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * create: php artisan make:seed CategoryTableSeeder
     * Run: php artisan db:seed --class=CategoryTableSeeder
     * @return void
     */

    public function run()
    {
        $category_default = [
            [
                'slug' => 'course',
                'position' => Category::POS_HEADER,
                'translations' => [
                    'vi' => [
                        'name' => 'Khóa Học',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Courses',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'exam',
                'position' => Category::POS_HEADER,
                'translations' => [
                    'vi' => [
                        'name' => 'Đề thi',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Exams',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'store',
                'position' => Category::POS_HEADER,
                'translations' => [
                    'vi' => [
                        'name' => 'Cửa hàng',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Store',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'job',
                'position' => Category::POS_HEADER,
                'translations' => [
                    'vi' => [
                        'name' => 'Việc làm',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Jobs',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'event',
                'position' => Category::POS_HEADER,
                'translations' => [
                    'vi' => [
                        'name' => 'Sự Kiện',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Event',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'center',
                'position' => Category::POS_HEADER,
                'translations' => [
                    'vi' => [
                        'name' => 'Trung Tâm',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Center',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'home',
                'position' => Category::POS_FOOTER,
                'translations' => [
                    'vi' => [
                        'name' => 'Trang Chủ',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Home',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'contact',
                'position' => Category::POS_FOOTER,
                'translations' => [
                    'vi' => [
                        'name' => 'Liên Hệ',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Contact',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'question',
                'position' => Category::POS_FOOTER,
                'translations' => [
                    'vi' => [
                        'name' => 'Câu hỏi',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Contact',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'payment',
                'position' => Category::POS_FOOTER,
                'translations' => [
                    'vi' => [
                        'name' => 'Hướng Dẫn Thanh Toán',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'How to payment',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'use',
                'position' => Category::POS_FOOTER,
                'translations' => [
                    'vi' => [
                        'name' => 'Hướng Dẫn Sử dụng',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'How to Use',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'about-us',
                'position' => Category::POS_FOOTER,
                'translations' => [
                    'vi' => [
                        'name' => 'Về chúng tôi',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'About Us',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'enterprise',
                'position' => Category::POS_FOOTER,
                'translations' => [
                    'vi' => [
                        'name' => 'Doanh nghiệp',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Enterprise',
                        'short_content' => ''
                    ]
                ]
            ],
            [
                'slug' => 'job',
                'position' => Category::POS_FOOTER,
                'translations' => [
                    'vi' => [
                        'name' => 'Việc làm',
                        'short_content' => ''
                    ],
                    'en' => [
                        'name' => 'Jobs',
                        'short_content' => ''
                    ]
                ]
            ],
        ];

        foreach ($category_default as $key => $cat_data){
            $new_data = [
                'sort' => $key + 1,
                'position' => $cat_data['position'],
                'created_by' => 1,
                'slug' => Str::slug($cat_data['slug'], '-'),
                'is_default' => 1
            ];
            $cat = Category::create($new_data);
            $language_keys = array_keys($cat_data['translations']);
            foreach ($language_keys as $language) {
                $cat->translateOrNew($language)->name = $cat_data['translations'][$language]['name'];
            }
            $cat->save();
        }
    }
}
