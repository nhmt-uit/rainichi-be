<?php

namespace App\Http\Controllers\API\LandingPage;

use App\Models\LandingContent;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\LandingContentAdminTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('patch')) {
            $content_id = $request->route('id');
            $data_change = $request->except('_method');
            $content = LandingContent::find($content_id);
            if ($content) {
                if ($request->translations) {
                    $language_keys = array_keys($data_change['translations']);
                    foreach ($language_keys as $language) {
                        $content->translateOrNew($language)->title = $data_change['translations'][$language]['title'];
                        $content->translateOrNew($language)->sub_title = $data_change['translations'][$language]['sub_title'];
                        $content->translateOrNew($language)->start_date = $data_change['translations'][$language]['start_date'];
                        $content->translateOrNew($language)->time_range = $data_change['translations'][$language]['time_range'];
                        $content->translateOrNew($language)->address = $data_change['translations'][$language]['address'];
                        $content->translateOrNew($language)->short_content = $data_change['translations'][$language]['short_content'];
                        $content->translateOrNew($language)->content = $data_change['translations'][$language]['content'];
                    }
                }

                //check image
                if ($request->hasFile('image')) {
                    $old_image = $content->image;
                    $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.landing_page_image_folder'));
                    $data_change['image'] = $image;
                    if ($old_image != null) {
                        UploadService::handleRemoveFile($old_image);
                    }
                }
                $data_change['updated_by'] = Auth::user()->id;
                $content->update($data_change);
                return BaseResponse::customResponse(
                    'Update successfully',
                    (new LandingContentAdminTransformer)->transform($content),
                    true,
                    200,
                    200,
                    'Success'
                );
            }
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function detail(Request $request)
    {
        $content_id = $request->route('id');
        $content = LandingContent::query()->find($content_id);
        if ($content) {
            return BaseResponse::customResponse(
                'Get data successfully',
                (new LandingContentAdminTransformer)->transform($content),
                true,
                200,
                200,
                'Success'
            );
        }
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
