<?php
/**
 * Created by PhpStorm.
 * User: tuduong
 * Date: 08/01/2019
 * Time: 13:58
 */


namespace App\Http\Controllers\API\Grammar\Sentence;


use App\Http\Controllers\Controller;
use App\Models\GrammarSentence;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use PHPUnit\Framework\ExpectationFailedException;

class DeleteController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $vocabulary = GrammarSentence::find($id);
                if ($vocabulary != null) {
                    $vocabulary->delete();
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
