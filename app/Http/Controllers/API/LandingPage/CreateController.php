<?php

namespace App\Http\Controllers\API\LandingPage;

use App\Http\Requests\LandingContentRequest;
use App\Models\LandingContent;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\LandingContentAdminTransformer;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * @param LandingContentRequest $request
     * @return JsonResponse
     */
    public function create(LandingContentRequest $request)
    {
        $landing_data = $request->all();
        //check image
        if ($request->hasFile('image')) {

            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.landing_page_image_folder'));
            $landing_data['image'] = $image;
        }

        $landing_data['created_by'] = Auth::user()->id;
        $landing = LandingContent::create($landing_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($landing_data['translations']);
        foreach ($language_keys as $language) {
            $landing->translateOrNew($language)->title = $landing_data['translations'][$language]['title'];
            $landing->translateOrNew($language)->sub_title = $landing_data['translations'][$language]['sub_title'] ?? '';
            $landing->translateOrNew($language)->start_date = $landing_data['translations'][$language]['start_date'] ?? '';
            $landing->translateOrNew($language)->time_range = $landing_data['translations'][$language]['time_range'] ?? '';
            $landing->translateOrNew($language)->address = $landing_data['translations'][$language]['address'] ?? '';
            $landing->translateOrNew($language)->short_content = $landing_data['translations'][$language]['short_content'] ?? '';
            $landing->translateOrNew($language)->content = $landing_data['translations'][$language]['content'] ?? '';
        }

        if ($landing->save()) {
            return BaseResponse::customResponse(
                'Create landing page successfully',
                (new LandingContentAdminTransformer)->transform($landing),
                true,
                200,
                201,
                'Created'
            );
        }else {
            return BaseResponse::customResponse(
                'Fail to insert new landing page',
                [],
                false,
                Config('error_constant.normal.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }
}
