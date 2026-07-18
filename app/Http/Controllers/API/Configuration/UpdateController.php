<?php

namespace App\Http\Controllers\API\Configuration;

use App\Models\Configuration;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use App\Http\Requests\ConfigurationRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * @param ConfigurationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ConfigurationRequest $request)
    {
        if ($request->isMethod('patch')) {
            $data_change = $request->except('_method');
            $config = Configuration::query()->first();
            if($config){
                if ($request->translations) {
                    $language_keys = array_keys($data_change['translations']);
                    foreach ($language_keys as $language) {
                        $config->translateOrNew($language)->title = $data_change['translations'][$language]['title'];
                        $config->translateOrNew($language)->slogan = $data_change['translations'][$language]['slogan'];
                        $config->translateOrNew($language)->keywords = $data_change['translations'][$language]['keywords'];
                        $config->translateOrNew($language)->description = $data_change['translations'][$language]['description'];
                        $config->translateOrNew($language)->address = $data_change['translations'][$language]['address'];
                        $config->translateOrNew($language)->payment_guide = $data_change['translations'][$language]['payment_guide'];
                        $config->translateOrNew($language)->payment_info = $data_change['translations'][$language]['payment_info'];
                    }
                }
                //check image
                if ($request->hasFile('logo')) {
                    $old_image = $config->logo;
                    $image = UploadService::handleUploadFile($request->file('logo'), Config('uploadpath.config_image_folder'));
                    $data_change['logo'] = $image;
                    if ($old_image != null) {
                        UploadService::handleRemoveFile($old_image);
                    }
                }
                $data_change['updated_by'] = Auth::user()->id;
                $config->update($data_change);
                if ($config->save()) {
                    return BaseResponse::customResponse(
                        'Update successfully',
                        $config,
                        true,
                        200,
                        200,
                        'Success'
                    );
                }
            }else{
                return BaseResponse::customResponse(
                    'Data not found',
                    [],
                    false,
                    404,
                    'NotFound'
                );
            }
        }
    }
}
