<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * Authentication Resources
 */
Route::get('/finder', 'API\Files\ListController@index');
Route::get('/push', 'API\DemoController@create');
Route::post('/finder-upload', 'API\Files\UploadController@uploadViaFinder');
Route::post('/finder-create-folder', 'API\Files\UploadController@createFolder');
Route::post('/finder-delete', 'API\Files\UploadController@removeViaFinder');

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', 'API\AuthController@login');
    Route::post('admin-login', 'API\AuthController@adminLogin');
    Route::post('login/refresh', 'API\AuthController@refresh');
    Route::get('social-login/{provider}', 'API\SocialAuthController@redirectToProvider');
    Route::post('social-login-mobile/{provider}', 'API\SocialAuthController@socialLoginForMobile');
    Route::get('social-callback/{provider}', 'API\SocialAuthController@callback');
    Route::post('signup', 'API\AuthController@sign_up');
    Route::patch('signup/activate', 'API\AuthController@signupActivate');
    Route::post('signup/verify-email', 'API\AuthController@verifyEmailExists');

    Route::group([
        'middleware' => 'auth:api',

    ], function () {
        Route::get('logout', 'API\AuthController@logout');
        Route::get('user', 'API\AuthController@user');
        Route::patch('user', 'API\AuthController@updateProfile');
        Route::post('update-password', 'API\PasswordResetController@updatePassword');
    });
    Route::group([

        'middleware' => 'api',
        'prefix' => 'password'
    ], function () {
        Route::post('create-reset', 'API\PasswordResetController@create');
    });
});
/**
 * Vocabulary Resources
 */
Route::group([
    'prefix' => 'vocabulary',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/', 'API\Vocabulary\ListController@index');
        Route::get('/in-lesson/{id}', 'API\Vocabulary\ListController@getListInLesson');
        Route::get('/admin', 'API\Vocabulary\ListController@getListAdmin');
        Route::post('/', 'API\Vocabulary\CreateController@create');
        Route::get('/{id}', 'API\Vocabulary\UpdateController@detail');
        Route::patch('/{id}', 'API\Vocabulary\UpdateController@update');
        Route::post('/delete', 'API\Vocabulary\DeleteController@delete');
    });
Route::get('voca-update', 'API\Vocabulary\UpdateController@updateAudio');
/**
 * Course Resources
 */
Route::group([
    'prefix' => 'course',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/', 'API\Course\ListController@index');
        Route::get('/price-type', 'API\Course\ListController@listPriceType');
        Route::get('/price_currency', 'API\Course\ListController@listPriceCurrency');
        Route::get('/admin', 'API\Course\ListController@getListCourseAdmin');
        Route::get('/parent', 'API\Course\ListController@getParentCourse');
        Route::get('/children/{id}', 'API\Course\ListController@getChildrenCourse');
        Route::get('/has-children', 'API\Course\ListController@getCourseTypeHasChildren');
        Route::get('/{id}', 'API\Course\DetailController@detail');
        Route::get('/admin/{id}', 'API\Course\DetailController@detailForAdmin');
        Route::get('/get-lesson-by/{course_id}', 'API\Course\DetailController@getLessonByCourseId');
        Route::get('/get-test-by/{course_id}', 'API\Course\DetailController@getTestByCourseId');
        Route::post('/', 'API\Course\CreateController@create');
        Route::post('/add-lesson/{id}', 'API\Course\CreateController@addLesson');
        Route::post('/add-test/{id}', 'API\Course\CreateController@addTest');
        Route::post('/{id}/lesson/following', 'API\Course\CreateController@addLessonFollowings');
        Route::patch('/{id}', 'API\Course\UpdateController@update');
        Route::post('/delete', 'API\Course\DeleteController@delete');
        Route::post('/delete-route/{id}', 'API\Course\DeleteController@deleteCourseRoute');
    });
Route::group([
    'prefix' => 'pure-route',
],
    function () {
        Route::get('/course', 'API\Course\ListController@index');
        Route::get('/course/admin', 'API\Course\ListController@getListCourseAdmin');
        Route::get('/test', 'API\Test\ListController@Index');
        Route::get('/list-of-test', 'API\Test\ListController@index');
        Route::get('/test/{id}', 'API\Test\UpdateController@detail');
        Route::get('/course-children/{id}', 'API\Course\ListController@getChildrenCourse');
        Route::get('/course/{id}', 'API\Course\DetailController@detail');
        Route::get('/level', 'API\Level\ListController@index');
        Route::get('/credit', 'API\Credit\ListController@index');
        Route::get('/course-type', 'API\CourseType\ListController@index');
    });

Route::group([
    'prefix' => 'mail-setting',
    'middleware' => 'auth:api',
    'namespace' => 'API\MailSetting',
], function () {
    Route::get('/', 'UpdateController@detail');
    Route::patch('/', 'UpdateController@update');
    Route::post('/send-mail', 'UpdateController@demo');
});
/**
 * CourseType Resources
 */
Route::group([
    'prefix' => 'course-type',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/', 'API\CourseType\ListController@index');
        Route::get('/{id}', 'API\CourseType\UpdateController@getCourseTypeById');
        Route::post('/', 'API\CourseType\CreateController@create');
        Route::post('/{id}', 'API\CourseType\CreateController@addLesson');
        Route::patch('/{id}', 'API\CourseType\UpdateController@update');
        Route::delete('/{id}', 'API\CourseType\DeleteController@delete');
    });
/**
 * Lesson Resources
 */
Route::group([
    'prefix' => 'lesson',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/admin', 'API\Lesson\ListController@index');
        Route::get('/admin/{id}', 'API\Lesson\UpdateController@getLessonDetail');
        Route::get('/skill', 'API\Lesson\ListController@getListSkill');
        Route::post('/', 'API\Lesson\CreateController@create');
        Route::patch('/{id}', 'API\Lesson\UpdateController@update');
        Route::patch('/in-course/{course_id}', 'API\Lesson\UpdateController@updateCourseLesson');
        Route::post('/delete', 'API\Lesson\DeleteController@delete');
        Route::post('/delete/in-course/{id}', 'API\Lesson\DeleteController@deleteInCourse');
        Route::post('/update/sort-in-course/{course_id}', 'API\Lesson\UpdateController@updateSortInCourse');
        Route::get('/get-vocabulary/{id}', 'API\Lesson\ListController@getListVocabulary');
    });

/**
 * Grammar resources
 */
Route::group([
    'prefix' => 'grammar',
    'middleware' => 'auth:api',
    'namespace' => 'API\Grammar',
],
    function () {
        Route::get('/', 'ListController@index');
        Route::get('/{id}', 'DetailController@detail');
        Route::post('/', 'CreateController@create');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
        Route::get('/in-group/{group_id}', 'ListController@getListInGroup');
        Route::get('/in-lesson/{lesson_id}', 'ListController@getListInLesson');
        Route::post('/add-to-group/{group_id}', 'CreateController@addToGroup');
        Route::post('/delete-ingroup/{group_id}', 'DeleteController@deleteInGroup');
        Route::group([
            'prefix' => '/{grammar_id}/sentence',
            'middleware' => 'auth:api',
            'namespace' => 'Sentence',
        ],
            function () {
                Route::get('/', 'ListController@index');
                Route::post('/', 'CreateController@create');
                Route::post('/delete', 'DeleteController@delete');
                Route::get('/{id}', 'DetailController@detail');
                Route::patch('/{id}', 'UpdateController@update');

            });
    });
/**
 * Foundation Resource
 */
Route::group([
    'prefix' => 'foundation',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/alphabet', 'API\Foundation\ListController@index');
        Route::post('/alphabet', 'API\Foundation\CreateController@createAlphabet');
        Route::get('/number', 'API\Foundation\ListController@listNumber');
        Route::post('/number', 'API\Foundation\CreateController@createNumber');
        Route::get('/', 'API\Foundation\ListController@listFoundation');
        Route::post('/', 'API\Foundation\CreateController@createDefinition');
    });

/**
 * Alphabet Resource
 */
Route::group([
    'prefix' => 'alphabet',
    'middleware' => 'auth:api',
    'namespace' => 'API\Alphabet'
],
    function () {
        Route::get('/{type}/{id}', 'ListController@index');
        Route::patch('/{type}/{id}', 'UpdateController@update');
        Route::delete('/{type}/{id}', 'DeleteController@delete');
        Route::get('/{type}', 'ListController@index');
        Route::post('/{type}', 'CreateController@create');
    });

Route::group([
    'prefix' => 'user',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/default-avatar', 'API\User\ListController@getListAvatarDefault');
        Route::get('/detail/{id}', 'API\User\UpdateController@detail');
        Route::patch('/{id}', 'API\User\UpdateController@update');
        Route::get('/list', 'API\User\ListController@getListUser');
        Route::get('/list-student', 'API\User\ListController@getListStudents');
        Route::get('/my-courses', 'API\User\ListController@myCourses');
        Route::get('/my-course-detail/{course_id}', 'API\User\ListController@myLessonInCourse');
        Route::post('/default-avatar', 'API\User\CreateController@createDefaultAvatar');
        Route::post('/', 'API\User\CreateController@create');
        Route::post('/delete', 'API\User\DeleteController@delete');
        Route::get('/my-tests', 'API\User\MyTestController@myTests');
        Route::get('/my-test-detail/{result_id}', 'API\User\MyTestController@myResultDetail');
        Route::post('/active-course', 'API\User\UpdateController@activeOrInActiveCourse');

    });


/**
 * Get user Unauthenticated
 */

Route::group([
    'prefix' => 'user',
],
    function () {
        Route::get('/teachers', 'API\User\ListController@getListTeachers');
    });

Route::group([
    'prefix' => 'language'
],
    function () {
        Route::get('/', 'API\Language\ListController@index');
    });
Route::group([
    'prefix' => 'level',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/', 'API\Level\ListController@index');
    });

/**
 * Group chapter resources
 */
Route::group([
    'prefix' => 'skill',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/', 'API\GroupChapter\ListController@index');
        Route::get('/{id}', 'API\GroupChapter\UpdateController@detail');
        Route::post('/', 'API\GroupChapter\CreateController@create');
        Route::patch('/{id}', 'API\GroupChapter\UpdateController@update');
        Route::post('/delete', 'API\GroupChapter\DeleteController@delete');
    });

Route::group([
    'prefix' => 'vocabulary-group',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/{id}', 'API\GroupChapter\ListController@listVocabulary');
        Route::post('/{id}', 'API\GroupChapter\CreateController@addVocabulary');
        Route::post('/delete/{id}', 'API\GroupChapter\DeleteController@deleteVocabulary');
    });

Route::group([
    'prefix' => 'group',
    'middleware' => 'auth:api',
],
    function () {
//        Route::get('/{id}', 'API\GroupChapter\ListController@listVocabulary');
        Route::post('/add-to-lesson/{id}', 'API\GroupChapter\CreateController@addToLesson');
        Route::post('/delete/in-lesson/{lesson_id}', 'API\GroupChapter\DeleteController@deleteInLesson');
    });

Route::group([
    'prefix' => 'kanji',
    'middleware' => 'auth:api',
    'namespace' => 'API\Kanji'
],
    function () {
        Route::get('/', 'ListController@index');
        Route::get('/in-group/{group_id}', 'ListController@getListInGroup');
        Route::get('/in-lesson/{lesson_id}', 'ListController@getListInLesson');
        Route::get('/all-in-lesson/{course_id}', 'ListController@getAllKanjiInView');
        Route::get('/admin', 'ListController@getListAdmin');
        Route::post('/', 'CreateController@create');
        Route::post('/add-to-group/{group_id}', 'CreateController@addToGroup');
        Route::get('/{id}', 'UpdateController@detail');
        Route::get('/text/{kanji}', 'UpdateController@detailByKanji');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
        Route::post('/delete-ingroup/{group_id}', 'DeleteController@deleteInGroup');
    });

/**
 * Conversation Resources
 */
Route::group([
    'prefix' => 'conversation',
    'middleware' => 'auth:api',
    'namespace' => 'API\Conversation'
],
    function () {
        Route::get('/admin', 'ListController@getListAdmin');
        Route::get('/in-group/{group_id}', 'ListController@getListInGroup');
        Route::get('/in-lesson/{lesson_id}', 'ListController@getListInLesson');
        Route::post('/', 'CreateController@create');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
        Route::post('/add-to-group/{group_id}', 'CreateController@addToGroup');
        Route::post('/delete-ingroup/{group_id}', 'DeleteController@deleteInGroup');
    });
/**
 * Conversation Resources
 */
Route::group([
    'prefix' => 'exercise',
    'middleware' => 'auth:api',
    'namespace' => 'API\Exercise'
],
    function () {
        Route::get('/admin', 'ListController@index');
        Route::get('/in-group/{group_id}', 'ListController@getListInGroup');
        Route::get('/in-lesson/{lesson_id}', 'ListController@getListInLesson');
        Route::post('/', 'CreateController@create');
        Route::post('/submit', 'CreateController@submitExercise');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
        Route::post('/add-to-group/{group_id}', 'CreateController@addToGroup');
        Route::post('/delete-ingroup/{group_id}', 'DeleteController@deleteInGroup');
    });

Route::group([
    'prefix' => 'listening',
    'middleware' => 'auth:api',
    'namespace' => 'API\Exercise'
],
    function () {
        Route::get('/admin', 'ListController@index');
        Route::get('/in-group/{group_id}', 'ListController@getListInGroup');
        Route::get('/in-lesson/{lesson_id}', 'ListController@getListInLesson');
        Route::post('/', 'CreateController@create');
        Route::post('/submit', 'CreateController@submitExercise');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
        Route::post('/add-to-group/{group_id}', 'CreateController@addToGroup');
        Route::post('/delete-ingroup/{group_id}', 'DeleteController@deleteInGroup');
    });
/**
 * Test resource
 */

Route::group([
    'prefix' => 'test',
    'middleware' => 'auth:api',
    'namespace' => 'API\Test'
],
    function () {
        Route::get('/', 'ListController@index');
        Route::get('/in-lesson/{id}', 'ListController@getListInLesson');
        Route::get('/admin', 'ListController@getListAdmin');
        Route::post('/', 'CreateController@create');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
        Route::post('/add-question-group', 'CreateController@addQuestionToTest');
        Route::post('/submit', 'CreateController@addQuestionToTest');
        Route::get('/get-question-by-tab/{test_time_id}', 'UpdateController@getQuestionByTestTimeId');
        Route::get('/get-question-group/{test_id}', 'ListController@getGroupInTestSection');
        Route::post('/delete-question-group/{test_time_id}', 'DeleteController@deleteQuestionGroupInTest');
        Route::post('/submit-test/{test_id}', 'CreateController@submitTest');
        #try to new logic for exam
        Route::post('/submit-new-test/{test_id}', 'CreateController@createTestResult');
        Route::post('/submit-test-detail/{test_result_id}', 'CreateController@submitTestDetails');

        Route::post('/delete-in-course/{id}', 'DeleteController@deleteInCourse');
        Route::post('/update/sort-in-course/{course_id}', 'UpdateController@updateSortInCourse');
    });


/**
 * Reading Resources
 */
Route::group([
    'prefix' => 'reading',
    'middleware' => 'auth:api',
    'namespace' => 'API\Exercise'
],
    function () {
        Route::get('/admin', 'ListController@index');
        Route::get('/in-group/{group_id}', 'ListController@getListInGroup');
        Route::get('/in-lesson/{lesson_id}', 'ListController@getListInLesson');
        Route::post('/', 'CreateController@create');
        Route::post('/submit', 'CreateController@submitExercise');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
        Route::post('/add-to-group/{group_id}', 'CreateController@addToGroup');
        Route::post('/delete-ingroup/{group_id}', 'DeleteController@deleteInGroup');
        Route::get('/list-question-in-reading/{reading_id}', 'ListController@getListQuestionById');
    });


/**
 * Reading Resources
 */
Route::group([
    'prefix' => 'credit',
    'middleware' => 'auth:api',
    'namespace' => 'API\Credit'
],
    function () {
        Route::get('/', 'ListController@index');
        Route::post('/', 'CreateController@create');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
    });

/**
 * Reading Resources
 */
Route::group([
    'prefix' => 'reward-rule',
    'namespace' => 'API\RewardRule'
],
    function () {
        Route::get('/', 'ListController@index');
        Route::post('/', 'CreateController@create')->middleware('auth:api');
        Route::get('/{id}', 'UpdateController@detail')->middleware('auth:api');
        Route::patch('/{id}', 'UpdateController@update')->middleware('auth:api');
        Route::post('/delete', 'DeleteController@delete')->middleware('auth:api');
    });

Route::group([
    'prefix' => 'payment',
    'middleware' => 'auth:api',
    'namespace' => 'API\Payment'
],
    function () {
        Route::post('/', 'CreateController@submitPayment');
    });

Route::group([
    'prefix' => 'payment-vnpay',
    'middleware' => 'auth:api',
    'namespace' => 'API\Payment'
],
    function () {
        Route::post('/', 'CreateController@submitVnPayment');
    });

Route::group([
    'prefix' => 'payment-iap',
    'middleware' => 'auth:api',
    'namespace' => 'API\Payment'
],
    function () {
        Route::post('/', 'IAPController@validateIAPReceipt');
    });

Route::get('/payment-vnpay/response', 'API\Payment\CreateController@getResponseVnPay');
Route::get('/payment-vnpay/ipn', 'API\Payment\IPNController@index');

Route::get('/payment/atm/response', 'API\Payment\CreateController@getResponseATM');
Route::get('/payment/visa/response', 'API\Payment\CreateController@getResponseVisa');
Route::get('/payment/atm/ipn', 'API\Payment\CreateController@ipnATM');
Route::get('/payment/visa/ipn', 'API\Payment\CreateController@ipnVisa');

#Buying course by cash
Route::group([
    'prefix' => 'payment-by-cash',
    'namespace' => 'API\Classroom\Payment'
], function () {
    Route::post('/', 'BuyingController@createPaymentRequest')->middleware('auth:api');
    Route::get('/response', 'BuyingController@getPaymentResponse');
});

Route::group([
    'prefix' => 'buying',
    'middleware' => 'auth:api',
    'namespace' => 'API\Payment'
],
    function () {
        Route::post('/course', 'BuyingController@buyCourse');
        Route::post('/test', 'BuyingController@buyCourse');
        Route::post('/offline', 'CreateController@offlinePayment');
        Route::post('/order-action/{action}/{order_id}', 'CreateController@approveOrRejectOrder');
    });
Route::group([
    'prefix' => 'order',
    'middleware' => 'auth:api',
    'namespace' => 'API\Order'
],
    function () {
        Route::get('/', 'ListController@index');
        Route::post('order-action/{order_id}/{action}', 'UpdateController@approveOrRejectOrder');
        Route::get('/payment', 'ListController@orderPayment');
        Route::post('/delete', 'DeleteController@deleteOrder');
        Route::post('/payment/delete', 'DeleteController@delete');
        Route::get('/user-transaction', 'ListController@getOrderListByUser');
    });
Route::group([
    'prefix' => 'permission',
    'middleware' => 'auth:api',
    'namespace' => 'API\Permission'
],
    function () {
        Route::get('/', 'ListController@index')->middleware('permission');
        Route::get('/import', 'ListController@importPermission');
    });
Route::group([
    'prefix' => 'upload',
//    'middleware' => 'auth:api',
    'namespace' => 'API\Files'
],
    function () {
        Route::post('/video', 'UploadController@uploadLargeVideoFile');
    });


/**
 * Article Resources
 */
Route::group([
    'prefix' => 'article',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/category', 'API\Article\ListController@getCategory');
        Route::get('/category/{type_id}', 'API\Article\ListController@getList');
        Route::get('/admin/', 'API\Article\ListController@getListAdmin');
        Route::get('/admin/apply', 'API\UserApply\ListController@getListApply');
        Route::get('/{id}', 'API\Article\UpdateController@detail');
        Route::get('/{id}/{admin?}', 'API\Article\UpdateController@detail');

        Route::post('/create', 'API\Article\CreateController@create');
        Route::patch('/{id}/update', 'API\Article\UpdateController@update');
        Route::patch('/{id}/view', 'API\Article\UpdateController@updateView');
        Route::post('/delete', 'API\Article\DeleteController@delete');
        Route::post('/apply/delete', 'API\UserApply\DeleteController@deleteApply');

        Route::patch('/admin/apply/{id}', 'API\UserApply\UpdateController@update');
        Route::get('/admin/apply/jobs', 'API\UserApply\ListController@getJobList');

    });

/**
 * Article Resources Unauthenticated
 */

Route::group([
    'prefix' => 'article',
], function () {
    Route::get('/{id}', 'API\Article\UpdateController@detail');
    Route::post('{id}/apply', 'API\UserApply\CreateController@createApplyJob');
});


/**
 * Slider Resources Unauthenticated
 */

Route::group([
    'prefix' => 'slider',
], function () {
    Route::get('/page', 'API\Slider\ListController@getByCategory');

});

/**
 * Slider Resources
 */

Route::group([
    'prefix' => 'slider',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/', 'API\Slider\ListController@index');
        Route::get('/admin/', 'API\Slider\ListController@getListAdmin');
        Route::post('/create', 'API\Slider\CreateController@create');
        Route::get('/{id}', 'API\Slider\UpdateController@detail');
        Route::patch('/{id}/update', 'API\Slider\UpdateController@update');
        Route::post('/delete', 'API\Slider\DeleteController@delete');
    });


/**
 * Slider Resources Unauthenticated
 */

Route::group([
    'prefix' => 'slider',
], function () {
    Route::get('/', 'API\Slider\ListController@index');

});


Route::group([
    'prefix' => 'configuration',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/', 'API\Configuration\ListController@index');
        Route::get('/admin', 'API\Configuration\ListController@index');
        Route::patch('/admin/update', 'API\Configuration\UpdateController@update');
    });

/**
 * Configuration Resources Unauthenticated
 */

Route::group([
    'prefix' => 'configuration',
], function () {
    Route::get('/', 'API\Configuration\ListController@index');
});


Route::get('/draw-kanji/{kanji}', 'API\Kanji\ListController@drawKanji');


/**
 * Category Resources Unauthenticated
 */

Route::group([
    'prefix' => 'category',
], function () {
    Route::get('/', 'API\Category\ListController@getList');
    Route::get('/articles', 'API\Category\ListController@getArticleListByCategory');
});

/**
 * Category Resources
 */
Route::group([
    'prefix' => 'category',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/position', 'API\Category\ListController@position');
        Route::get('/admin/', 'API\Category\ListController@getListAdmin');
        Route::get('/admin/tree', 'API\Category\ListController@getListTree');
        Route::post('/create', 'API\Category\CreateController@create');
        Route::get('/{id}', 'API\Category\UpdateController@detail');
        Route::patch('/{id}/update', 'API\Category\UpdateController@update');
        Route::post('/delete', 'API\Category\DeleteController@delete');
    });

/**
 * Country Resources
 */
Route::group([
    'prefix' => 'country',
    'middleware' => 'auth:api',
],
    function () {
        Route::get('/', 'API\Country\ListController@getList');
    });

/**
 * Company Resources
 */
Route::group([
    'prefix' => 'company',
    'middleware' => 'auth:api',
    'namespace' => 'API\Company'
],
    function () {
        Route::get('/admin', 'ListController@getListAdmin');
        Route::post('/create', 'CreateController@create');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');

        Route::get('{id}/admins', 'User\ListController@getListCompanyAdmin');
        Route::get('{id}/users', 'User\ListController@getListCompanyUser');
        Route::post('{id}/users/', 'User\CreateController@addCompanyUser');
        Route::post('{id}/users/delete', 'User\DeleteController@deleteCompanyUser');
    });

/**
 * Classroom Resources
 */
Route::group([
    'prefix' => 'classroom',
    'middleware' => 'auth:api',
    'namespace' => 'API\Classroom'
],
    function () {
        #FE
        Route::get('/checked', 'UpdateController@checkedExistClass');
        Route::get('/admin', 'ListController@getListAdmin');
        Route::post('/create', 'CreateController@create');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');

        #user in class that include Students
        Route::group([
            'prefix' => '{id}/users',
            'namespace' => 'User'
        ], function () {
            Route::get('/', 'ListController@getListUserClass');
            Route::post('/delete', 'DeleteController@deleteUserClass');
            Route::post('/', 'CreateController@addUserClass');
            Route::patch('/{class_user_id}', 'UpdateController@update');

        });

        #course in class that include Course
        Route::group([
            'prefix' => '{id}/courses',
            'namespace' => 'Course'
        ], function () {
            Route::get('/', 'ListController@getListCourseClass');
            Route::get('/calculated', 'UpdateController@getCalculated');
            Route::post('/delete/{class_course_id}', 'DeleteController@deleteCourseClass');
            Route::patch('/{class_course_id}', 'UpdateController@update');
            Route::post('/', 'CreateController@addCourseClass');

        });

        #exams in class that include Exam
        Route::group([
            'prefix' => '{id}/exams',
            'namespace' => 'Course'
        ], function () {
            Route::get('/', 'ListController@getListCourseClass');
            Route::get('/calculated', 'UpdateController@getCalculatedExam');
            Route::post('delete', 'DeleteController@deleteCourseClass');
            Route::post('/', 'CreateController@addCourseClass');
            Route::patch('/{class_course_id}', 'UpdateController@update');
        });

        Route::group([
            'prefix' => '{id}/statistic',
            'namespace' => 'Statistical'
        ], function () {
            Route::get('/', 'ListController@index');
        });
    });

/**
 * Contact Resources
 */
Route::group([
    'prefix' => 'contact',
    'middleware' => 'auth:api',
    'namespace' => 'API\Contact'
],
    function () {
        Route::post('/', 'CreateController@create');
        Route::get('/type', 'ListController@getContactList');
        Route::get('/admin', 'ListController@getListAdmin');
        Route::post('delete', 'DeleteController@delete');
        Route::get('/{id}', 'UpdateController@detail');
    });

/**
 * Contact Resources Unauthenticated
 */

Route::group([
    'prefix' => 'contact',
    'namespace' => 'API\Contact'
], function () {
    Route::post('/', 'CreateController@create');
});

/**
 * Get Course Resource Unauthenticated
 */

Route::group([
    'prefix' => 'landing-page',
],
    function () {
        Route::get('/course', 'API\Course\ListController@getCourseLandingPage');
    });

/**
 * Promotion Resources
 */
Route::group([
    'prefix' => 'promotion',
    'middleware' => 'auth:api',
    'namespace' => 'API\Promotion'
],
    function () {
        Route::get('/admin', 'ListController@getListAdmin');
        Route::post('/create', 'CreateController@create');
        Route::patch('/{id}', 'UpdateController@update');
        Route::post('/delete', 'DeleteController@delete');
        Route::get('/{id}', 'UpdateController@detail');
    });

/**
 * Landing-page Resources
 */
Route::group([
    'prefix' => 'landing-page',
    'middleware' => 'auth:api',
    'namespace' => 'API\LandingPage'
],
    function () {
        Route::get('/page', 'ListController@getListPage');
        Route::get('/type', 'ListController@getListType');
        Route::get('/parent', 'ListController@getListContentParent');
        Route::get('/content', 'ListController@getListContent');
        Route::post('/', 'CreateController@create');
        Route::get('/admin', 'ListController@getListAdmin');
        Route::post('/delete', 'DeleteController@delete');
        Route::get('/{id}', 'UpdateController@detail');
        Route::patch('/{id}', 'UpdateController@update');
    });

/**
 * Landing-page Resources Unauthenticated
 */

Route::group([
    'prefix' => 'landing-page',
    'namespace' => 'API\LandingPage'
], function () {
    Route::get('/', 'ListController@getListContent');
});

/**
 * Dashboard
 */
Route::group([
    'prefix' => 'dashboard',
    'middleware' => 'auth:api',
    'namespace' => 'API\Dashboard'
],
    function () {
        Route::get('/total', 'ListController@getTotal');
        Route::get('/best-selling-course', 'ListController@bestSellingCourse');
        Route::get('/recent-student', 'ListController@recentStudents');
        Route::get('/recent-order', 'ListController@recentOrderClass');
    });

Route::group([
    'prefix' => 'event-day',
    'middleware' => 'auth:api',
    'namespace' => 'API\EventDay'
], function () {
    Route::get('/', 'CreateController@list');
    Route::post('/', 'CreateController@index');
});
Route::group([
    'prefix' => 'share',
    'middleware' => 'auth:api',
    'namespace' => 'API\Share'
], function () {
    Route::post('/', 'CreateController@index');
});
/**
 * Notification
 */
Route::group([
    'prefix' => 'notification',
    'middleware' => 'auth:api',
], function () {
    Route::get('/', 'API\Notification\NotificationController@index');
    Route::post('/', 'API\Notification\NotificationController@create');
    Route::get('/admin', 'API\Notification\NotificationController@getListNotificationAdmin');
    Route::post('/delete', 'API\Notification\NotificationController@delete');
    Route::get('/{id}', 'API\Notification\NotificationController@detail');
    Route::post('/detail-by-token-device', 'API\Notification\NotificationController@detail_by_token_device');
    Route::patch('/{id}', 'API\Notification\NotificationController@update');
    Route::get('/admin/{id}', 'API\Notification\NotificationController@detailForAdmin');
});
/**
 * user token device
 */
Route::group([
    'prefix' => 'user-token-device',
    'middleware' => 'auth:api',
], function () {
    Route::get('/', 'API\UserTokenDevice\UserTokenDeviceController@index');
    Route::get('/admin', 'API\UserTokenDevice\UserTokenDeviceController@getListAdmin');
    Route::post('/delete', 'API\UserTokenDevice\UserTokenDeviceController@delete');
    Route::get('/{id}', 'API\UserTokenDevice\UserTokenDeviceController@detail');
    Route::post('/detail-by-token-device', 'API\UserTokenDevice\UserTokenDeviceController@detail_by_token_device');
    Route::post('/', 'API\UserTokenDevice\UserTokenDeviceController@create');
    Route::post('/{id}', 'API\UserTokenDevice\UserTokenDeviceController@update');
});
/**
 * Group User
 */
Route::group([
    'prefix' => 'group-user',
    'middleware' => 'auth:api',
], function () {
    Route::get('/', 'API\GroupUser\GroupUserController@index');
    Route::post('/', 'API\GroupUser\GroupUserController@create');
    Route::get('/admin', 'API\GroupUser\GroupUserController@getListForAdmin');
    Route::post('/delete', 'API\GroupUser\GroupUserController@delete');
    Route::get('/{id}', 'API\GroupUser\GroupUserController@detail');
    Route::patch('/{id}', 'API\GroupUser\GroupUserController@update');
    Route::get('/admin/{id}', 'API\GroupUser\GroupUserController@detailForAdmin');
});
/**
 * List User By Group User
 */
Route::group([
    'prefix' => 'list-user-by-group-user',
    'middleware' => 'auth:api',
], function () {
    Route::get('/', 'API\ListUserByGroupUser\ListUserByGroupUserController@index');
    Route::post('/', 'API\ListUserByGroupUser\ListUserByGroupUserController@create');
    Route::get('/admin', 'API\ListUserByGroupUser\ListUserByGroupUserController@getListForAdmin');
    Route::post('/delete', 'API\ListUserByGroupUser\ListUserByGroupUserController@delete');
    Route::get('/{id}', 'API\ListUserByGroupUser\ListUserByGroupUserController@detail');
    Route::patch('/{id}', 'API\ListUserByGroupUser\ListUserByGroupUserController@update');
    Route::get('/admin/{id}', 'API\ListUserByGroupUser\ListUserByGroupUserController@detailForAdmin');
});
