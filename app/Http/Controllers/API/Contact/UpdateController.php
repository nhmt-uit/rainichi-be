<?php

namespace App\Http\Controllers\API\Contact;

use App\Models\Contact;
use App\Service\BaseResponse;
use App\Transformers\ContactTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        $contact_id = $request->route('id');
        $contact = Contact::query()->find($contact_id);
        if ($contact) {
            $contact->update(['is_active'=> 1]);
            return BaseResponse::customResponse(
                'Get data successfully',
                (new ContactTransformer)->transform($contact),
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                __('Data not found'),
                [],
                false,
                Config('error_constant.article.not_found'),
                404,
                'NotFound'
            );
        }
    }
}
