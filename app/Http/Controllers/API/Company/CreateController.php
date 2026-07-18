<?php

namespace App\Http\Controllers\API\Company;

use App\Transformers\CompanyAdminTransformer;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Http\Requests\CompanyRequest;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * Add new company
     * @param CompanyRequest $request
     * @return \Illuminate\Http\JsonResponse;
     */
    public function create(CompanyRequest $request)
    {
        try {
            $company_data = $request->all();
            //check image
            if ($request->hasFile('image')) {

                $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.company_image_folder'));
                $company_data['image'] = $image;
            }
            $company_data['created_by'] = Auth::user()->id;
            $company = Company::create($company_data);
            $language_keys = array_keys($company_data['translations']);
            foreach ($language_keys as $language) {
                $company->translateOrNew($language)->name = $company_data['translations'][$language]['name'];
                $company->translateOrNew($language)->career = $company_data['translations'][$language]['career'];
                $company->translateOrNew($language)->content = $company_data['translations'][$language]['content'];
            }
            $company->save();
            return BaseResponse::customResponse(
                'Create article successfully',
                (new CompanyAdminTransformer)->transform($company),
                true,
                201,
                201,
                'Created'
            );
        } catch (\Exception $e) {
            return BaseResponse::customResponse(
                'Fail to insert new company',
                [],
                false,
                Config('error_constant.article.insert_fail'),
                500,
                'Unprocessable Entity'
            );
        }
    }


}
