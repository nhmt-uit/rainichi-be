<?php


namespace App\Http\Controllers\API\UserApply;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserApplyRequest;
use App\Models\Article;
use App\Models\UserApply;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateController extends Controller
{

    /**
     * @param UserApplyRequest $request
     * @return JsonResponse
     */

    public function createApplyJob(UserApplyRequest $request)
    {
        $article_id = $request->route('id');
        $article = Article::query()->find($article_id);
        if ($article) {
            try {
                $nameFile = '';
                if($request->hasFile('cv')){
                    $nameFile = $request->file('cv')->getClientOriginalName();
                }
                $dataApply = $request->all();
                $dataApply['article_id'] = $article->id;
                $dataApply['user_id'] = Auth::user()->id ?? null;
                $dataApply['ip_address'] = $request->ip() ?? 'Unknown';
                $dataApply['user_agent'] = $request->userAgent() ?? 'Unknown';
                $dataApply['cv_name'] = $nameFile;
                $userApply = UserApply::query()->where('email', $dataApply['email'])
                    ->where('article_id', $article_id)->first();
                if (empty($userApply)) {
                    //check image
                    if ($request->hasFile('cv')) {
                        $file = UploadService::handleUploadFile($request->file('cv'), Config('uploadpath.article_cv_folder'));
                        $dataApply['cv'] = $file;
                    }
                    UserApply::create($dataApply);
                    return BaseResponse::customResponse(
                        'Apply successfully',
                        null,
                        true,
                        200,
                        201,
                        'Created'
                    );
                } else {
                    return BaseResponse::customResponse(
                        'Fail to apply job. Current user applied this job',
                        [],
                        false,
                        Config('error_constant.article.insert_fail'),
                        422,
                        'Unprocessable Entity'
                    );
                }
            } catch (Exception $e) {
                Log::error($e->getMessage());
                return BaseResponse::customResponse(
                    'Something went wrong',
                    [],
                    false,
                    Config('error_constant.article.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Job not found',
                [],
                false,
                Config('error_constant.article.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }

    }
}
