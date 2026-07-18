<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('https://rainichi.vn');
});

Route::get('/command', function () {
    passthru('sudo php -v', $out);
    echo $out;
});


Route::get('/bai-viet/{slug}/{type}', 'PostController@detail');

Route::get('/demo', function () {
    return view('reset-password');
});
Route::get('/payment', function () {
    return view('payment.payment-result');
});
//
//Route::post('/demo-upload', 'API\Files\UploadController@uploadLargeVideoFile');


Route::get('password/find/{token}', 'API\PasswordResetController@find');
Route::post('password/reset', 'API\PasswordResetController@reset');
Route::get('user/verify/{time}/{token}', 'API\AuthController@verificationEmail');
