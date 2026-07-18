<?php

namespace App\Http\Controllers\API\Contact;

use App\Http\Requests\ContactRequest;
use App\Service\BaseResponse;
use App\Http\Controllers\Controller;
use App\Models\Contact;

class CreateController extends Controller
{

    /**
     *Add new Contact
     * @param ContactRequest $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create(ContactRequest $request)
    {
        $contactData = $request->all();
        $contactData['ip_address'] = $request->ip() ?? 'Unknown';
        $contactData['user_agent'] = $request->userAgent() ?? 'Unknown';
        if (key_exists('course_id', $contactData) && is_array($contactData['course_id'])) {
            $contactData['course_id'] = json_encode($contactData['course_id']);
        }
        $contact = Contact::create($contactData);
        if ($contact) {
            return BaseResponse::customResponse(
                'Add contact successfully',
                '',
                true,
                200,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to add contact',
                '',
                false,
                Config('error_constant.normal.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }
}
