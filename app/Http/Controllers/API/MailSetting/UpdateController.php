<?php


namespace App\Http\Controllers\API\MailSetting;


use App\Http\Controllers\Controller;
use App\Http\Requests\MailSettingRequest;
use App\Models\MailSetting;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UpdateController extends Controller
{
    /**
     * Get mail setting
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail()
    {
        return BaseResponse::customResponse(
            'Success',
            MailSetting::query()->first(),
            true,
            200,
            200,
            'Success',
            []
        );
    }

    /**
     * Update email setting info
     * @param MailSettingRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(MailSettingRequest $request)
    {
        $mail = MailSetting::query()->first();
        if ($mail) {
            $data = $request->all();
            $mail->update($data);
            return BaseResponse::customResponse(
                'Success',
                $mail,
                true,
                200,
                200,
                'Success',
                []
            );
        }
    }


    /**
     * Send mail demo
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function demo(Request $request)
    {
        $email = $request->get('email');
        $title = $request->get('title');
        $content = $request->get('content');
        Mail::send('mail.demo', ['content' => $content], function ($message) use ($email, $title) {
            $message->to($email, 'Rainichi')->subject($title);
        });
        return BaseResponse::customResponse(
            'Success',
            [],
            true,
            200,
            200,
            'Success',
            []
        );
    }
}
