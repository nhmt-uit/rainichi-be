<?php

namespace App\Http\Controllers\API\Category;

use App\Models\Category;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteController extends Controller
{
    /**
     * Delete category by id.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        $excepted_id = [];
        $messageDefault = 'Delete completed';
        $status = true;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $category = Category::find($id);
                if ($category != null && !$category->is_default) {
                    $category->delete();
                }else
                {
                    array_push($excepted_id, $id);
                }
            }
            if(sizeof($excepted_id) > 0){
               $messageDefault = 'Some ID can not delete because of it\'s value default';
               $status = false;
            }

            return BaseResponse::customResponse(
                $messageDefault,
                [],
                $status,
                Config('error_constant.normal.delete_fail'),
                200,
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
