<?php

namespace App\Http\Controllers\API\Contact;

use App\Models\Contact;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteController extends Controller
{
    /**
     * Delete contact by id.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $contact = Contact::find($id);
                if ($contact != null) {
                    $contact->delete();
                }
            }
            return BaseResponse::customResponse(
                'Deleted contact is successfully',
                [],
                true,
                200,
                202,
                'Accepted'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to delete',
                [],
                false,
                Config('error_constant.normal.delete_fail'),
                422,
                'Unprocessable Entity'
            );

        }

    }
}
