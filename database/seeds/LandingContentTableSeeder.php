<?php


use App\Models\LandingContent;
use App\Models\LandingPage;
use App\Models\LandingType;
use App\Service\UploadService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LandingContentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * composer dump-autoload
     * Run: php artisan db:seed --class=LandingContentTableSeeder
     * @return void
     */
    public function run()
    {
        $landing_content_default = [
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                'image' => 'landing_page/images/strong_app.png',
                'video' => 1,
                'sort' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Điểm mạnh của ứng dụng',
                        'sub_title' => 'Tiếng nhật khó đã có Rainichi'
                    ],
                    'en' => [
                        'title' => 'Điểm mạnh của ứng dụng',
                        'sub_title' => 'Tiếng nhật khó đã có Rainichi'
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Ứng dụng học tiếng Nhật hàng đầu Việt Nam tích hợp công nghệ nhận dạng giọng nói trí tuệ nhân tạo thông minh',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Ứng dụng học tiếng Nhật hàng đầu Việt Nam tích hợp công nghệ nhận dạng giọng nói trí tuệ nhân tạo thông minh',
                                    'sub_title' => ''
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Nội dung khóa học được xây dựng bởi đội ngủ giảng viên tiếng Nhật lâu năm nhiều kinh nghiệm trong giảng dạy tiếng Nhật',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Nội dung khóa học được xây dựng bởi đội ngủ giảng viên tiếng Nhật lâu năm nhiều kinh nghiệm trong giảng dạy tiếng Nhật',
                                    'sub_title' => ''
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Ứng dụng đầu tiên tích hợp thi thử năng lực Nhật Ngữ JLPT cùng kho đề thi đa dạng đầy đủ nhất hiện nay',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Ứng dụng đầu tiên tích hợp thi thử năng lực Nhật Ngữ JLPT cùng kho đề thi đa dạng đầy đủ nhất hiện nay',
                                    'sub_title' => ''
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Tích hợp tính năng quản lý lớp học dành cho doanh nghiệp và trường học trong quản lý tiến độ học tập của học viên',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Tích hợp tính năng quản lý lớp học dành cho doanh nghiệp và trường học trong quản lý tiến độ học tập của học viên',
                                    'sub_title' => ''
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Cung cấp cơ hội việc làm và du học tại Nhật Bản với tính năng tìm việc cùng thông tin du học đa dạng.',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Cung cấp cơ hội việc làm và du học tại Nhật Bản với tính năng tìm việc cùng thông tin du học đa dạng.',
                                    'sub_title' => ''
                                ]
                            ],
                        ]
                    ]
            ], // Tinh nang ung dung
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                'index' => null,
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Giới thiệu tính năng ứng dụng',
                        'sub_title' => 'Cho cá nhân - Doanh Nghiệp - Trường học',
                        'content' => 'Tìm trung tâm, tìm giáo viên,
                                    tìm cách học hay là tìm cho mình một khóa học online phù hợp,
                                    điều bạn cần là hãy cố gắng và nỗ lực thực sự để vượt
                                    qua các rào cản và đạt được hiệu quả học tập tốt nhất.
                                    Đó là lý do mà sự phù hợp luôn là yếu tố không thể bỏ qua để mang lại
                                    hiệu quả tốt nhất có thể cho người học.
                                    Rainichi đem đến cho bạn một khóa học với các bài giảng xuyên suốt các chủ đề
                                    rõ ràng và quen thuộc với hầu hết những kĩ năng cần thiết.
                                    Bên cạnh đó phù hợp cho mọi người, là học sinh hoặc cả những người bận bịu với công việc,
                                    với các cấp độ phục vụ cho mọi đối tượng từ cơ bản đến nâng cao.
                                    Chúng tôi tin rằng tìm đến Rainichi là sự lựa chọn hoàn hảo của bạn.'
                    ],
                    'en' => [
                        'title' => 'Giới thiệu tính năng ứng dụng',
                        'sub_title' => 'Cho cá nhân - Doanh Nghiệp - Trường học',
                        'content' => 'Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.'
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                            'image' => 'landing_page/images/course.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Khóa học',
                                    'sub_title' => '',
                                    'content' => 'Đa dạng theo mọi trình độ từ N5 đến N1'
                                ],
                                'en' => [
                                    'title' => 'Khóa học',
                                    'sub_title' => '',
                                    'content' => 'Đa dạng theo mọi trình độ từ N5 đến N1'
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                            'image' => 'landing_page/images/class.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Lớp học',
                                    'sub_title' => '',
                                    'content' => 'Tính năng lớp học dành riêng cho doanh nghiệp, trường học'
                                ],
                                'en' => [
                                    'title' => 'Lớp học',
                                    'sub_title' => '',
                                    'content' => 'Tính năng lớp học dành riêng cho doanh nghiệp, trường học'
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                            'image' => 'landing_page/images/skill.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Kỹ năng toàn diện',
                                    'sub_title' => '',
                                    'content' => 'Từ vựng, ngữ pháp, đọc hiểu, nghe, thoại hội, luyện tập'
                                ],
                                'en' => [
                                    'title' => 'Kỹ năng toàn diện',
                                    'sub_title' => '',
                                    'content' => 'Từ vựng, ngữ pháp, đọc hiểu, nghe, thoại hội, luyện tập'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                            'image' => 'landing_page/images/sound.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Luyện phát âm',
                                    'sub_title' => '',
                                    'content' => 'Ứng dụng công nghệ hàng đầu kiểm tra phát âm theo giọng bản xứ'
                                ],
                                'en' => [
                                    'title' => 'Luyện phát âm',
                                    'sub_title' => '',
                                    'content' => 'Ứng dụng công nghệ hàng đầu kiểm tra phát âm theo giọng bản xứ'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                            'image' => 'landing_page/images/chinese.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Chữ hán',
                                    'sub_title' => '',
                                    'content' => 'Tính năng thông minh hỗ trợ nhớ nhanh Hán Tự'
                                ],
                                'en' => [
                                    'title' => 'Chữ hán',
                                    'sub_title' => '',
                                    'content' => 'Tính năng thông minh hỗ trợ nhớ nhanh Hán Tự'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                            'image' => 'landing_page/images/test.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Đề thi',
                                    'sub_title' => '',
                                    'content' => 'Phong phú, đa dạng mọi trình độ JLPT N5 - N1'
                                ],
                                'en' => [
                                    'title' => 'Đề thi',
                                    'sub_title' => '',
                                    'content' => 'Phong phú, đa dạng mọi trình độ JLPT N5 - N1'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                            'image' => 'landing_page/images/job.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Việc làm',
                                    'sub_title' => '',
                                    'content' => 'Cơ hội việc làm tiếng Nhật miễn phí tại Nhật Bản, Việt Nam'
                                ],
                                'en' => [
                                    'title' => 'Việc làm',
                                    'sub_title' => '',
                                    'content' => 'Cơ hội việc làm tiếng Nhật miễn phí tại Nhật Bản, Việt Nam'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::INTRO_APP)->first()->id, # Intro
                            'image' => 'landing_page/images/study.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Du học',
                                    'sub_title' => '',
                                    'content' => 'Thông tin chi tiết về học bổng du học, chương trình du học tại Nhật Bản'
                                ],
                                'en' => [
                                    'title' => 'Du học',
                                    'sub_title' => '',
                                    'content' => 'Thông tin chi tiết về học bổng du học, chương trình du học tại Nhật Bản'
                                ],
                            ],
                        ],
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::ONLINE_COURSE)->first()->id, # Intro
                'index' => null,
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Khóa học trực tuyến',
                        'sub_title' => '',
                        'content' => 'Không khoảng cách, không giới hạn thời gian, tính ứng dụng cao,
                                        không những vậy khoá học trực tuyến còn giúp bạn tăng tính độc lập trong việc học.
                                        Rainichih cung cấp cho bạn một khóa học với các trình độ và kĩ năng,
                                        đáp ứng như cầu của mọi người.'
                    ],
                    'en' => [
                        'title' => 'Khóa học trực tuyến',
                        'sub_title' => '',
                        'content' => 'Không khoảng cách, không giới hạn thời gian, tính ứng dụng cao,
                                        không những vậy khoá học trực tuyến còn giúp bạn tăng tính độc lập trong việc học.
                                        Rainichih cung cấp cho bạn một khóa học với các trình độ và kĩ năng,
                                        đáp ứng như cầu của mọi người.'
                    ]
                ],
                'childs' => []
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::CENTER)->first()->id, # Intro
                'index' => null,
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Trung tâm tiếng Nhật Rainichi',
                        'sub_title' => '',
                        'content' => 'Trung tâm Nhật Ngữ Rainichi được thành 
                                    lập nhằm hỗ trợ nhu cầu học cấp tốc tiếng Nhật
                                    qua Nhật Bản du học và làm việc.
                                    Bên cạnh sử dụng ứng dụng Rainichi, các khoá học trục tiếp tại trung tâm sẽ hỗ trợ học viên
                                    đạt trình độ N3 trong 6 tháng và cải thiện khả năng giao tiếp bản xứ cùng đội ngũ giảng viên
                                     kinh nghiệm của trung tâm.'
                    ],
                    'en' => [
                        'title' => 'Trung tâm tiếng Nhật Rainichi',
                        'sub_title' => '',
                        'content' => 'Trung tâm Nhật Ngữ Rainichi được thành 
                                    lập nhằm hỗ trợ nhu cầu học cấp tốc tiếng Nhật
                                    qua Nhật Bản du học và làm việc.
                                    Bên cạnh sử dụng ứng dụng Rainichi, các khoá học trục tiếp tại trung tâm sẽ hỗ trợ học viên
                                    đạt trình độ N3 trong 6 tháng và cải thiện khả năng giao tiếp bản xứ cùng đội ngũ giảng viên
                                     kinh nghiệm của trung tâm.'
                    ]
                ],
                'childs' => []
            ],
            // User
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Người dùng',
                        'sub_title' => 'Nói về ứng dụng Rainichi',
                        'content' => ''
                    ],
                    'en' => [
                        'title' => 'Người dùng',
                        'sub_title' => 'Nói về ứng dụng Rainichi',
                        'content' => ''
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/vi_ngo.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Vy Ngô',
                                    'sub_title' => 'Làm việc tại Osaka, Nhật Bản',
                                    'content' => '
                                      Rainichi giúp cho việc học tiếng Nhật trở nên hiệu quả,
                                      cung cấp một kho đề thi và kiến thức đồ sộ,
                                      dành cho những bạn mới làm quen với tiếng Nhật đến những bạn
                                      chuẩn bị tham gia các kì thi năng lực tiếng Nhật.'
                                ],
                                'en' => [
                                    'title' => 'Vy Ngô',
                                    'sub_title' => 'Làm việc tại Osaka, Nhật Bản',
                                    'content' => '
                                      Rainichi giúp cho việc học tiếng Nhật trở nên hiệu quả,
                                      cung cấp một kho đề thi và kiến thức đồ sộ,
                                      dành cho những bạn mới làm quen với tiếng Nhật đến những bạn
                                      chuẩn bị tham gia các kì thi năng lực tiếng Nhật.'
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/minh_chau.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Minh Châu',
                                    'sub_title' => 'Làm việc tại Gunma, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ],
                                'en' => [
                                    'title' => 'Minh Châu',
                                    'sub_title' => 'Làm việc tại Gunma, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/anh_tuan.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Anh Tuấn',
                                    'sub_title' => 'Làm việc tại Tokyo, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ],
                                'en' => [
                                    'title' => 'Anh Tuấn',
                                    'sub_title' => 'Làm việc tại Tokyo, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ]
                            ],
                        ]
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Đối tác',
                        'sub_title' => 'Khách hàng của Rainichi',
                        'content' => '
                                        Rainichi không ngừng chú trọng phát triển nội dung nhằm đạt chất
                                        lượng cao, luôn lắng nghe phản hồi của khách hàng và hành động,
                                        ngày càng góp phần nâng cao lòng tin của khách hàng.
                                        Động lực để đạt được chất lượng cao sẽ dễ dàng và nhanh
                                        chóng hơn nhiều khi bạn có các đối tác kinh doanh phù hợp.'
                    ],
                    'en' => [
                        'title' => 'Đối tác',
                        'sub_title' => 'Khách hàng của Rainichi',
                        'content' => '
                                        Rainichi không ngừng chú trọng phát triển nội dung nhằm đạt chất
                                        lượng cao, luôn lắng nghe phản hồi của khách hàng và hành động,
                                        ngày càng góp phần nâng cao lòng tin của khách hàng.
                                        Động lực để đạt được chất lượng cao sẽ dễ dàng và nhanh
                                        chóng hơn nhiều khi bạn có các đối tác kinh doanh phù hợp.'
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_cept.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_skazzy.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_poyii.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_kayo.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_issuler.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::INDEX_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_issuler.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ]
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::CENTER_COURSE)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Các khóa học',
                        'sub_title' => 'Tại Rainichi',
                        'content' => ''
                    ],
                    'en' => [
                        'title' => 'Các khóa học',
                        'sub_title' => 'Tại Rainichi',
                        'content' => ''
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CENTER_COURSE)->first()->id, # Partner
                            'image' => 'landing_page/images/class_fast_n3.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Lớp cấp tốc 6 tháng đạt N3',
                                    'sub_title' => 'Đào tạo từ vỡ lòng',
                                ],
                                'en' => [
                                    'title' => 'Lớp cấp tốc 6 tháng đạt N3',
                                    'sub_title' => 'Đào tạo từ vỡ lòng',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CENTER_COURSE)->first()->id, # Partner
                            'image' => 'landing_page/images/class_fast_4_n3.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Lớp cấp tốc 4 tháng đạt N3',
                                    'sub_title' => 'Đào tạo từ trình độ N5',
                                ],
                                'en' => [
                                    'title' => 'Lớp cấp tốc 6 tháng đạt N3',
                                    'sub_title' => 'Đào tạo từ trình độ N5',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CENTER_COURSE)->first()->id, # Partner
                            'image' => 'landing_page/images/class_fast_2_n3.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Lớp cấp tốc 2 tháng đạt N3',
                                    'sub_title' => 'Đào tạo từ trình độ N4',
                                ],
                                'en' => [
                                    'title' => 'Lớp cấp tốc 2 tháng đạt N3',
                                    'sub_title' => 'Đào tạo từ trình độ N4',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CENTER_COURSE)->first()->id, # Partner
                            'image' => 'landing_page/images/class_fast_6_n2.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Lớp cấp tốc 6 tháng đạt N2',
                                    'sub_title' => 'Đào tạo  từ trình độ N3',
                                ],
                                'en' => [
                                    'title' => 'Lớp cấp tốc 6 tháng đạt N2',
                                    'sub_title' => 'Đào tạo  từ trình độ N3',
                                ]
                            ],
                        ],
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'BẠN ĐƯỢC ĐÀO TẠO GÌ TẠI RAINICHI',
                        'sub_title' => '',
                        'content' => ''
                    ],
                    'en' => [
                        'title' => 'BẠN ĐƯỢC ĐÀO TẠO GÌ TẠI RAINICHI',
                        'sub_title' => '',
                        'content' => ''
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/fast.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Thần tốc',
                                    'sub_title' => '',
                                    'content' => 'Đạt trình độ N4, N3 và N2 từ vỡ lòng nhanh nhất Việt Nam'
                                ],
                                'en' => [
                                    'title' => 'Thần tốc',
                                    'sub_title' => '',
                                    'content' => 'Đạt trình độ N4, N3 và N2 từ vỡ lòng nhanh nhất Việt Nam'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/quality.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Chất lượng',
                                    'sub_title' => '',
                                    'content' => 'Giúp học viên nắm vững kiến thức từ nền tảng đến nâng cao trong thời gian cực nhanh'
                                ],
                                'en' => [
                                    'title' => 'Chất lượng',
                                    'sub_title' => '',
                                    'content' => 'Giúp học viên nắm vững kiến thức từ nền tảng đến nâng cao trong thời gian cực nhanh'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/confident.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Tự Tin',
                                    'sub_title' => '',
                                    'content' => 'Thực hành xử lý tình huống thực tế với giáo viên bản xứ'
                                ],
                                'en' => [
                                    'title' => 'Tự Tin',
                                    'sub_title' => '',
                                    'content' => 'Thực hành xử lý tình huống thực tế với giáo viên bản xứ'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/environment.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Môi trường',
                                    'sub_title' => '',
                                    'content' => 'Các hoạt động ngoại khóa thường xuyên giúp học viên giải trý và rèn luyện tiếng Nhật'
                                ],
                                'en' => [
                                    'title' => 'Môi trường',
                                    'sub_title' => '',
                                    'content' => 'Các hoạt động ngoại khóa thường xuyên giúp học viên giải trý và rèn luyện tiếng Nhật'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/job.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Việc làm',
                                    'sub_title' => '',
                                    'content' => 'Nhiều cơ hội việc làm tại NHẬT BẢN theo chường trình Engineer in Japan, Work in Japan, ... do trung tâm hợp tác tổ chức.'
                                ],
                                'en' => [
                                    'title' => 'Việc làm',
                                    'sub_title' => '',
                                    'content' => 'Nhiều cơ hội việc làm tại NHẬT BẢN theo chường trình Engineer in Japan, Work in Japan, ... do trung tâm hợp tác tổ chức.'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/study.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Học bổng - Du học',
                                    'sub_title' => '',
                                    'content' => 'MIỄN PHÍ tư vấn nhiều chương trình Học bổng - Du học tại Nhật Bản được liên kết cùng Rainichi'
                                ],
                                'en' => [
                                    'title' => 'Học bổng - Du học',
                                    'sub_title' => '',
                                    'content' => 'MIỄN PHÍ tư vấn nhiều chương trình Học bổng - Du học tại Nhật Bản được liên kết cùng Rainichi'
                                ],
                            ],
                        ],
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::OPENING_COURSE)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'CÁC KHÓA HỌC',
                        'sub_title' => 'SẮP KHAI GIẢNG TẠI TRUNG TÂM',
                        'content' => 'Cùng Rainichi khám phá siêu năng lực bản thân !!!!!'
                    ],
                    'en' => [
                        'title' => 'CÁC KHÓA HỌC',
                        'sub_title' => 'SẮP KHAI GIẢNG TẠI TRUNG TÂM',
                        'content' => 'Cùng Rainichi khám phá siêu năng lực bản thân !!!!!'
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::OPENING_COURSE)->first()->id, # Partner
                            'image' => 'landing_page/images/opening_n4_n3.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Khóa cấp tốc N4 lên N3',
                                    'sub_title' => '',
                                    'content' => 'Đào tạo cấp tốc trong vòng 2 tháng từ trình độ N4 lên trung cấp N3.Tự tin giao tiếp cùng người bản xứ.',
                                    'time_range' => '8:00 AM - 5:00 PM',
                                    'address' => 'Ho Chi Minh, Viet Nam',
                                    'start_date' => '11-04-2019',
                                ],
                                'en' => [
                                    'title' => 'Khóa cấp tốc N4 lên N3',
                                    'sub_title' => '',
                                    'content' => 'Đào tạo cấp tốc trong vòng 2 tháng từ trình độ N4 lên trung cấp N3.Tự tin giao tiếp cùng người bản xứ.',
                                    'time_range' => '8:00 AM - 5:00 PM',
                                    'address' => 'Ho Chi Minh, Viet Nam',
                                    'start_date' => '11-04-2019',
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::OPENING_COURSE)->first()->id, # Partner
                            'image' => 'landing_page/images/opening_0_n3.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Khóa cấp tốc Zero lên N3',
                                    'sub_title' => '',
                                    'content' => 'Đào tạo cấp tốc trong vòng 6 tháng từ trình độ vỡ lòng lên trung cấp N3.Đào tạo từ vựng chuyên ngành và luyện thi JLPT.',
                                    'time_range' => '8:00 AM - 5:00 PM',
                                    'address' => 'Ho Chi Minh, Viet Nam',
                                    'start_date' => '10-04-2019',
                                ],
                                'en' => [
                                    'title' => 'Khóa cấp tốc Zero lên N3',
                                    'sub_title' => '',
                                    'content' => 'Đào tạo cấp tốc trong vòng 6 tháng từ trình độ vỡ lòng lên trung cấp N3.Đào tạo từ vựng chuyên ngành và luyện thi JLPT.',
                                    'time_range' => '8:00 AM - 5:00 PM',
                                    'address' => 'Ho Chi Minh, Viet Nam',
                                    'start_date' => '10-04-2019',
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::OPENING_COURSE)->first()->id, # Partner
                            'image' => 'landing_page/images/opening_n3_n2.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Khóa cấp tốc N3 lên N2',
                                    'sub_title' => '',
                                    'content' => 'Đào tạo cấp tốc trong vòng 6 tháng từ trình độ N3 lên trung thượng cấp N2. Đào tạo từ vựng chuyên ngành và luyệt thi JLPT.',
                                    'time_range' => '8:00 AM - 5:00 PM',
                                    'address' => 'Ho Chi Minh, Viet Nam',
                                    'start_date' => '18-07-2019',
                                ],
                                'en' => [
                                    'title' => 'Khóa cấp tốc N3 lên N2',
                                    'sub_title' => '',
                                    'content' => 'Đào tạo cấp tốc trong vòng 6 tháng từ trình độ N3 lên trung thượng cấp N2. Đào tạo từ vựng chuyên ngành và luyệt thi JLPT.',
                                    'time_range' => '8:00 AM - 5:00 PM',
                                    'address' => 'Ho Chi Minh, Viet Nam',
                                    'start_date' => '18-07-2019',
                                ],
                            ],
                        ],
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::JOIN_TEAM)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'GIA NHẬP',
                        'sub_title' => 'TEAM RAINICHI',
                        'content' => 'Cùng Rainichi khám phá siêu năng lực bản thân !!!!!'
                    ],
                    'en' => [
                        'title' => 'GIA NHẬP',
                        'sub_title' => 'TEAM RAINICHI',
                        'content' => 'Cùng Rainichi khám phá siêu năng lực bản thân !!!!!'
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::JOIN_TEAM)->first()->id, # Partner
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'ƯU ĐÃI 20% KHI ĐĂNG KÝ TRƯỚC 20/05/2019 GIẢM THÊM 5% KHI ĐÓNG TRỌN KHÓA N3 HAY N2',
                                ],
                                'en' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'ƯU ĐÃI 20% KHI ĐĂNG KÝ TRƯỚC 20/05/2019 GIẢM THÊM 5% KHI ĐÓNG TRỌN KHÓA N3 HAY N2',
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::JOIN_TEAM)->first()->id, # Partner
                            'image' => 'landing_page/images/opening_n4_n3.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'Đạt trình độ Nhật ngữ N3 & N2 nhanh nhất Việt Nam',
                                ],
                                'en' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'Đạt trình độ Nhật ngữ N3 & N2 nhanh nhất Việt Nam',
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::JOIN_TEAM)->first()->id, # Partner
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'Làm chủ tiếng Nhật, Tự tin giao tiếp cùng người Bản ngữ',
                                ],
                                'en' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'Làm chủ tiếng Nhật, Tự tin giao tiếp cùng người Bản ngữ',
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::JOIN_TEAM)->first()->id, # Partner
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'Tham gia phỏng vấn việc làm tại Nhật Bản MIỄN PHÍ',
                                ],
                                'en' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'Tham gia phỏng vấn việc làm tại Nhật Bản MIỄN PHÍ',
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::JOIN_TEAM)->first()->id, # Partner
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'Cơ hội nhận Học bổng du học Nhật Bản 100% ',
                                ],
                                'en' => [
                                    'title' => 'GIA NHẬP SUB',
                                    'content' => 'Cơ hội nhận Học bổng du học Nhật Bản 100% ',
                                ],
                            ],
                        ],
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::REGISTER_NOW)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'register_time' => Carbon::now()->addDays(5),
                'translations' => [
                    'vi' => [
                        'title' => 'ĐĂNG KÝ NGAY',
                        'sub_title' => '',
                        'content' => '',

                    ],
                    'en' => [
                        'title' => 'ĐĂNG KÝ NGAY',
                        'sub_title' => '',
                        'content' => '',
                    ]
                ],
                'childs' =>
                    [
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::CENTER)->first()->id, # Intro
                'index' => null,
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Trung tâm tiếng Nhật Rainichi',
                        'sub_title' => '',
                        'content' => 'Trung tâm Nhật Ngữ Rainichi được thành 
                                    lập nhằm hỗ trợ nhu cầu học cấp tốc tiếng Nhật
                                    qua Nhật Bản du học và làm việc.
                                    Bên cạnh sử dụng ứng dụng Rainichi, các khoá học trục tiếp tại trung tâm sẽ hỗ trợ học viên
                                    đạt trình độ N3 trong 6 tháng và cải thiện khả năng giao tiếp bản xứ cùng đội ngũ giảng viên
                                     kinh nghiệm của trung tâm.'
                    ],
                    'en' => [
                        'title' => 'Trung tâm tiếng Nhật Rainichi',
                        'sub_title' => '',
                        'content' => 'Trung tâm Nhật Ngữ Rainichi được thành 
                                    lập nhằm hỗ trợ nhu cầu học cấp tốc tiếng Nhật
                                    qua Nhật Bản du học và làm việc.
                                    Bên cạnh sử dụng ứng dụng Rainichi, các khoá học trục tiếp tại trung tâm sẽ hỗ trợ học viên
                                    đạt trình độ N3 trong 6 tháng và cải thiện khả năng giao tiếp bản xứ cùng đội ngũ giảng viên
                                     kinh nghiệm của trung tâm.'
                    ]
                ],
                'childs' => []
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Người dùng',
                        'sub_title' => 'Nói về ứng dụng Rainichi',
                        'content' => ''
                    ],
                    'en' => [
                        'title' => 'Người dùng',
                        'sub_title' => 'Nói về ứng dụng Rainichi',
                        'content' => ''
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/vi_ngo.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Vy Ngô',
                                    'sub_title' => 'Làm việc tại Osaka, Nhật Bản',
                                    'content' => '
                                      Rainichi giúp cho việc học tiếng Nhật trở nên hiệu quả,
                                      cung cấp một kho đề thi và kiến thức đồ sộ,
                                      dành cho những bạn mới làm quen với tiếng Nhật đến những bạn
                                      chuẩn bị tham gia các kì thi năng lực tiếng Nhật.'
                                ],
                                'en' => [
                                    'title' => 'Vy Ngô',
                                    'sub_title' => 'Làm việc tại Osaka, Nhật Bản',
                                    'content' => '
                                      Rainichi giúp cho việc học tiếng Nhật trở nên hiệu quả,
                                      cung cấp một kho đề thi và kiến thức đồ sộ,
                                      dành cho những bạn mới làm quen với tiếng Nhật đến những bạn
                                      chuẩn bị tham gia các kì thi năng lực tiếng Nhật.'
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/minh_chau.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Minh Châu',
                                    'sub_title' => 'Làm việc tại Gunma, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ],
                                'en' => [
                                    'title' => 'Minh Châu',
                                    'sub_title' => 'Làm việc tại Gunma, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/anh_tuan.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Anh Tuấn',
                                    'sub_title' => 'Làm việc tại Tokyo, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ],
                                'en' => [
                                    'title' => 'Anh Tuấn',
                                    'sub_title' => 'Làm việc tại Tokyo, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ]
                            ],
                        ]
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Đối tác',
                        'sub_title' => 'Khách hàng của Rainichi',
                        'content' => '
                                        Rainichi không ngừng chú trọng phát triển nội dung nhằm đạt chất
                                        lượng cao, luôn lắng nghe phản hồi của khách hàng và hành động,
                                        ngày càng góp phần nâng cao lòng tin của khách hàng.
                                        Động lực để đạt được chất lượng cao sẽ dễ dàng và nhanh
                                        chóng hơn nhiều khi bạn có các đối tác kinh doanh phù hợp.'
                    ],
                    'en' => [
                        'title' => 'Đối tác',
                        'sub_title' => 'Khách hàng của Rainichi',
                        'content' => '
                                        Rainichi không ngừng chú trọng phát triển nội dung nhằm đạt chất
                                        lượng cao, luôn lắng nghe phản hồi của khách hàng và hành động,
                                        ngày càng góp phần nâng cao lòng tin của khách hàng.
                                        Động lực để đạt được chất lượng cao sẽ dễ dàng và nhanh
                                        chóng hơn nhiều khi bạn có các đối tác kinh doanh phù hợp.'
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_cept.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_skazzy.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_poyii.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_kayo.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_issuler.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::CENTER_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_issuler.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',setUserIdParams
                                ]
                            ],
                        ]
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                'image' => 'landing_page/images/strong_app.png',
                'video' => 1,
                'sort' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Điểm mạnh của ứng dụng',
                        'sub_title' => 'Tiếng nhật khó đã có Rainichi'
                    ],
                    'en' => [
                        'title' => 'Điểm mạnh của ứng dụng',
                        'sub_title' => 'Tiếng nhật khó đã có Rainichi'
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Ứng dụng học tiếng Nhật hàng đầu Việt Nam tích hợp công nghệ nhận dạng giọng nói trí tuệ nhân tạo thông minh',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Ứng dụng học tiếng Nhật hàng đầu Việt Nam tích hợp công nghệ nhận dạng giọng nói trí tuệ nhân tạo thông minh',
                                    'sub_title' => ''
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Nội dung khóa học được xây dựng bởi đội ngủ giảng viên tiếng Nhật lâu năm nhiều kinh nghiệm trong giảng dạy tiếng Nhật',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Nội dung khóa học được xây dựng bởi đội ngủ giảng viên tiếng Nhật lâu năm nhiều kinh nghiệm trong giảng dạy tiếng Nhật',
                                    'sub_title' => ''
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Ứng dụng đầu tiên tích hợp thi thử năng lực Nhật Ngữ JLPT cùng kho đề thi đa dạng đầy đủ nhất hiện nay',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Ứng dụng đầu tiên tích hợp thi thử năng lực Nhật Ngữ JLPT cùng kho đề thi đa dạng đầy đủ nhất hiện nay',
                                    'sub_title' => ''
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Tích hợp tính năng quản lý lớp học dành cho doanh nghiệp và trường học trong quản lý tiến độ học tập của học viên',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Tích hợp tính năng quản lý lớp học dành cho doanh nghiệp và trường học trong quản lý tiến độ học tập của học viên',
                                    'sub_title' => ''
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::STRONG_APP)->first()->id, # Strong
                            'image' => 'landing_page/images/check.svg',
                            'sort' => 1,
                            'video' => 1,
                            'translations' => [
                                'vi' => [
                                    'content' => 'Cung cấp cơ hội việc làm và du học tại Nhật Bản với tính năng tìm việc cùng thông tin du học đa dạng.',
                                    'sub_title' => ''
                                ],
                                'en' => [
                                    'content' => 'Cung cấp cơ hội việc làm và du học tại Nhật Bản với tính năng tìm việc cùng thông tin du học đa dạng.',
                                    'sub_title' => ''
                                ]
                            ],
                        ]
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::ONLINE_COURSE)->first()->id, # Intro
                'index' => null,
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Khóa học trực tuyến',
                        'sub_title' => '',
                        'content' => 'Không khoảng cách, không giới hạn thời gian, tính ứng dụng cao,
                                        không những vậy khoá học trực tuyến còn giúp bạn tăng tính độc lập trong việc học.
                                        Rainichih cung cấp cho bạn một khóa học với các trình độ và kĩ năng,
                                        đáp ứng như cầu của mọi người.'
                    ],
                    'en' => [
                        'title' => 'Khóa học trực tuyến',
                        'sub_title' => '',
                        'content' => 'Không khoảng cách, không giới hạn thời gian, tính ứng dụng cao,
                                        không những vậy khoá học trực tuyến còn giúp bạn tăng tính độc lập trong việc học.
                                        Rainichih cung cấp cho bạn một khóa học với các trình độ và kĩ năng,
                                        đáp ứng như cầu của mọi người.'
                    ]
                ],
                'childs' => []
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::JOIN_TEAM)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'GIA NHẬP',
                        'sub_title' => 'TEAM RAINICHI',
                        'content' => 'Cùng Rainichi khám phá siêu năng lực bản thân !!!!!'
                    ],
                    'en' => [
                        'title' => 'GIA NHẬP',
                        'sub_title' => 'TEAM RAINICHI',
                        'content' => 'Cùng Rainichi khám phá siêu năng lực bản thân !!!!!'
                    ]
                ],
                'childs' =>
                    [
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'BẠN ĐƯỢC ĐÀO TẠO GÌ TẠI RAINICHI',
                        'sub_title' => '',
                        'content' => ''
                    ],
                    'en' => [
                        'title' => 'BẠN ĐƯỢC ĐÀO TẠO GÌ TẠI RAINICHI',
                        'sub_title' => '',
                        'content' => ''
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/fast.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Thần tốc',
                                    'sub_title' => '',
                                    'content' => 'Đạt trình độ N4, N3 và N2 từ vỡ lòng nhanh nhất Việt Nam'
                                ],
                                'en' => [
                                    'title' => 'Thần tốc',
                                    'sub_title' => '',
                                    'content' => 'Đạt trình độ N4, N3 và N2 từ vỡ lòng nhanh nhất Việt Nam'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/quality.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Chất lượng',
                                    'sub_title' => '',
                                    'content' => 'Giúp học viên nắm vững kiến thức từ nền tảng đến nâng cao trong thời gian cực nhanh'
                                ],
                                'en' => [
                                    'title' => 'Chất lượng',
                                    'sub_title' => '',
                                    'content' => 'Giúp học viên nắm vững kiến thức từ nền tảng đến nâng cao trong thời gian cực nhanh'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/confident.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Tự tin',
                                    'sub_title' => '',
                                    'content' => 'Thực hành xử lý tình huống thực tế với giáo viên bản xứ'
                                ],
                                'en' => [
                                    'title' => 'Tự tin',
                                    'sub_title' => '',
                                    'content' => 'Thực hành xử lý tình huống thực tế với giáo viên bản xứ'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/environment.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Môi trường',
                                    'sub_title' => '',
                                    'content' => 'Các hoạt động ngoại khóa thường xuyên giúp học viên giải trý và rèn luyện tiếng Nhật'
                                ],
                                'en' => [
                                    'title' => 'Môi trường',
                                    'sub_title' => '',
                                    'content' => 'Các hoạt động ngoại khóa thường xuyên giúp học viên giải trý và rèn luyện tiếng Nhật'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/job.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Việc làm',
                                    'sub_title' => '',
                                    'content' => 'Nhiều cơ hội việc làm tại NHẬT BẢN theo chường trình Engineer in Japan, Work in Japan, ... do trung tâm hợp tác tổ chức.'
                                ],
                                'en' => [
                                    'title' => 'Việc làm',
                                    'sub_title' => '',
                                    'content' => 'Nhiều cơ hội việc làm tại NHẬT BẢN theo chường trình Engineer in Japan, Work in Japan, ... do trung tâm hợp tác tổ chức.'
                                ],
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::EDUCATION)->first()->id, # Partner
                            'image' => 'landing_page/images/study.svg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Học bổng - Du học',
                                    'sub_title' => '',
                                    'content' => 'MIỄN PHÍ tư vấn nhiều chương trình Học bổng - Du học tại Nhật Bản được liên kết cùng Rainichi'
                                ],
                                'en' => [
                                    'title' => 'Học bổng - Du học',
                                    'sub_title' => '',
                                    'content' => 'MIỄN PHÍ tư vấn nhiều chương trình Học bổng - Du học tại Nhật Bản được liên kết cùng Rainichi'
                                ],
                            ],
                        ],
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::CENTER)->first()->id, # Intro
                'index' => null,
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Trung tâm tiếng Nhật Rainichi',
                        'sub_title' => '',
                        'content' => 'Trung tâm Nhật Ngữ Rainichi được thành 
                                    lập nhằm hỗ trợ nhu cầu học cấp tốc tiếng Nhật
                                    qua Nhật Bản du học và làm việc.
                                    Bên cạnh sử dụng ứng dụng Rainichi, các khoá học trục tiếp tại trung tâm sẽ hỗ trợ học viên
                                    đạt trình độ N3 trong 6 tháng và cải thiện khả năng giao tiếp bản xứ cùng đội ngũ giảng viên
                                     kinh nghiệm của trung tâm.'
                    ],
                    'en' => [
                        'title' => 'Trung tâm tiếng Nhật Rainichi',
                        'sub_title' => '',
                        'content' => 'Trung tâm Nhật Ngữ Rainichi được thành 
                                    lập nhằm hỗ trợ nhu cầu học cấp tốc tiếng Nhật
                                    qua Nhật Bản du học và làm việc.
                                    Bên cạnh sử dụng ứng dụng Rainichi, các khoá học trục tiếp tại trung tâm sẽ hỗ trợ học viên
                                    đạt trình độ N3 trong 6 tháng và cải thiện khả năng giao tiếp bản xứ cùng đội ngũ giảng viên
                                     kinh nghiệm của trung tâm.'
                    ]
                ],
                'childs' => []
            ],
            // User
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Người dùng',
                        'sub_title' => 'Nói về ứng dụng Rainichi',
                        'content' => ''
                    ],
                    'en' => [
                        'title' => 'Người dùng',
                        'sub_title' => 'Nói về ứng dụng Rainichi',
                        'content' => ''
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/vi_ngo.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Vy Ngô',
                                    'sub_title' => 'Làm việc tại Osaka, Nhật Bản',
                                    'content' => '
                                      Rainichi giúp cho việc học tiếng Nhật trở nên hiệu quả,
                                      cung cấp một kho đề thi và kiến thức đồ sộ,
                                      dành cho những bạn mới làm quen với tiếng Nhật đến những bạn
                                      chuẩn bị tham gia các kì thi năng lực tiếng Nhật.'
                                ],
                                'en' => [
                                    'title' => 'Vy Ngô',
                                    'sub_title' => 'Làm việc tại Osaka, Nhật Bản',
                                    'content' => '
                                      Rainichi giúp cho việc học tiếng Nhật trở nên hiệu quả,
                                      cung cấp một kho đề thi và kiến thức đồ sộ,
                                      dành cho những bạn mới làm quen với tiếng Nhật đến những bạn
                                      chuẩn bị tham gia các kì thi năng lực tiếng Nhật.'
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/minh_chau.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Minh Châu',
                                    'sub_title' => 'Làm việc tại Gunma, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ],
                                'en' => [
                                    'title' => 'Minh Châu',
                                    'sub_title' => 'Làm việc tại Gunma, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::CUSTOMER)->first()->id, # Intro
                            'image' => 'landing_page/images/anh_tuan.jpg',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Anh Tuấn',
                                    'sub_title' => 'Làm việc tại Tokyo, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ],
                                'en' => [
                                    'title' => 'Anh Tuấn',
                                    'sub_title' => 'Làm việc tại Tokyo, Nhật Bản',
                                    'content' => '
                                                              Mình đã chọn học tiếng Nhật tại Rainichi vì thời gian đạt trình độ
                                                              N3 tại trung tâm nhanh nhất hiện nay.
                                                              Trong thời gian học tại trung tâm,
                                                              mình đã có cơ hội phỏng vấn với các công ty đến
                                                              từ Nhật Bản và tá đa, mình hiện đang làm việc tại
                                                              Nhật hơn 1 năm rồi.
                                                              Điều làm mình ấn tượng là các vị trí tuyển dụng tại Rainichi
                                                              đều hoàn toàn miễn phí.
                                                              Các anh chị tại trung tâm rất nhiệt tình và cẩn thận,
                                                              từ việc làm giấy tờ thủ tục đến giải đáp mọi thắc mắc về
                                                              cuộc sống ở Nhật. Điều đó chẳng phải rất tuyệt vời sao !?
                                                              Mình chỉ muốn nhắn nhủ với các bạn - 
                                                              những thế hệ trẻ năng động, luôn muốn khẳng định bản thân -
                                                              rằng là hãy cứ trau dồi bản thân đi, không ngừng học hỏi,
                                                              luôn chuẩn bị tinh thần sẵn sàng để khi có cơ hội đến thì
                                                              hãy cố gắng nắm bắt nó nhé.
                                                              '
                                ]
                            ],
                        ]
                    ]
            ],
            [
                'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                'sort' => 1,
                'video' => 1,
                'translations' => [
                    'vi' => [
                        'title' => 'Đối tác',
                        'sub_title' => 'Khách hàng của Rainichi',
                        'content' => '
                                        Rainichi không ngừng chú trọng phát triển nội dung nhằm đạt chất
                                        lượng cao, luôn lắng nghe phản hồi của khách hàng và hành động,
                                        ngày càng góp phần nâng cao lòng tin của khách hàng.
                                        Động lực để đạt được chất lượng cao sẽ dễ dàng và nhanh
                                        chóng hơn nhiều khi bạn có các đối tác kinh doanh phù hợp.'
                    ],
                    'en' => [
                        'title' => 'Đối tác',
                        'sub_title' => 'Khách hàng của Rainichi',
                        'content' => '
                                        Rainichi không ngừng chú trọng phát triển nội dung nhằm đạt chất
                                        lượng cao, luôn lắng nghe phản hồi của khách hàng và hành động,
                                        ngày càng góp phần nâng cao lòng tin của khách hàng.
                                        Động lực để đạt được chất lượng cao sẽ dễ dàng và nhanh
                                        chóng hơn nhiều khi bạn có các đối tác kinh doanh phù hợp.'
                    ]
                ],
                'childs' =>
                    [
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_cept.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_skazzy.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_poyii.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_kayo.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_issuler.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ],
                        [
                            'landing_page_id' => LandingPage::query()->where('type', LandingPage::ENTERPRISE_PAGE)->first()->id, # Index
                            'landing_type_id' => LandingType::query()->where('type', LandingType::PARTNER)->first()->id, # Partner
                            'image' => 'landing_page/images/customer_issuler.png',
                            'sort' => 1,
                            'video' => null,
                            'translations' => [
                                'vi' => [
                                    'title' => 'Partner',
                                ],
                                'en' => [
                                    'title' => 'Partner',
                                ]
                            ],
                        ]
                    ]
            ],

        ];
        LandingContent::query()->delete();
        self::createContent($landing_content_default);
    }

    static function createContent($landing_contents, $parent_id = null)
    {
        foreach ($landing_contents as $key => $landing_content) {

            $new_data = [
                'sort' => $landing_content['sort'] ?? 1,
                'landing_type_id' => $landing_content['landing_type_id'] ?? 1,
                'landing_page_id' => $landing_content['landing_page_id'] ?? 1,
                'parent_id' => $parent_id ?? null,
                'image' => $landing_content['image'] ?? null,
                'register_time' => $landing_content['register_time'] ?? null
            ];

            $content = LandingContent::create($new_data);
            $language_keys = array_keys($landing_content['translations']);
            foreach ($language_keys as $language) {
                $content->translateOrNew($language)->title = preg_replace('/\s\s+/', ' ', $landing_content['translations'][$language]['title'] ?? null);
                $content->translateOrNew($language)->sub_title = preg_replace('/\s\s+/', ' ', $landing_content['translations'][$language]['sub_title'] ?? null);
                $content->translateOrNew($language)->content = preg_replace('/\s\s+/', ' ', $landing_content['translations'][$language]['content'] ?? null);
                $content->translateOrNew($language)->short_content = preg_replace('/\s\s+/', ' ', $landing_content['translations'][$language]['short_content'] ?? null);
                $content->translateOrNew($language)->start_date = preg_replace('/\s\s+/', ' ', $landing_content['translations'][$language]['start_date'] ?? null);
                $content->translateOrNew($language)->time_range = preg_replace('/\s\s+/', ' ', $landing_content['translations'][$language]['time_range'] ?? null);
                $content->translateOrNew($language)->address = preg_replace('/\s\s+/', ' ', $landing_content['translations'][$language]['address'] ?? null);
            }
            $content->save();
            if (key_exists('childs', $landing_content) && sizeof($landing_content['childs']) > 0) {
                self::createContent($landing_content['childs'], $content->id);
            }
        }
    }

}
