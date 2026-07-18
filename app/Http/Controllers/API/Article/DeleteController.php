<?php


namespace App\Http\Controllers\API\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;


class DeleteController extends Controller
{
    /**
     * Delete vocabulary by id.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $article = Article::find($id);
                if ($article != null) {
                    $image = $article->image;
                    if ($article->delete()) {
                        if ($image != null) {
                            UploadService::handleRemoveFile($image);
                        }
                    }
                }
            }
            return BaseResponse::customResponse(
                'Deleted',
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
                Config('error_constant.vocabulary.delete_fail'),
                422,
                'Unprocessable Entity'
            );

        }

    }
}
