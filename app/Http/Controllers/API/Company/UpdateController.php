<?php

namespace App\Http\Controllers\API\Company;

use App\Models\Company;
use App\Models\User;
use App\Service\CompanyService;
use App\Service\UploadService;
use App\Transformers\CompanyAdminTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use App\Service\BaseResponse;

class UpdateController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CompanyAdminTransformer
     */
    private $companyAdminTransformer;

    function __construct(Manager $fractal, CompanyAdminTransformer $companyAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->companyAdminTransformer = $companyAdminTransformer;
    }

    /**
     * Get company details by id.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request)
    {
        $company_id = $request->route('id');
        $data_change = $request->except('_method');
        $company = Company::query()->find($company_id);
        if ($company) {
            //check image
            if ($request->hasFile('image')) {
                $old_image = $company->image;
                $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.company_image_folder'));
                $data_change['image'] = $image;
                if ($old_image != null) {
                    UploadService::handleRemoveFile($old_image);
                }
            }
            $data_change['updated_by'] = Auth::user()->id;
            $company->update($data_change);
            if (key_exists('is_active', $data_change) && Auth::user()->type == User::ADMIN) {
                CompanyService::updateRelateToCompany($company_id, $company->is_active);
            }
            $language_keys = array_keys($data_change['translations']);
            foreach ($language_keys as $language) {
                $company->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                $company->translateOrNew($language)->career = $data_change['translations'][$language]['career'];
                $company->translateOrNew($language)->content = $data_change['translations'][$language]['content'];
            }
            $company->save();
            return BaseResponse::customResponse(
                'Update successfully',
                (new CompanyAdminTransformer)->transform($company),
                true,
                200,
                200,
                'Success'
            );

        }
        return BaseResponse::customResponse(
            'Data not found',
            [],
            false,
            Config('error_constant.article.not_found'),
            404,
            'Not Found'
        );

    }

    /**
     * Get company details by id.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function detail(Request $request)
    {
        $company_id = $request->route('id');
        $company = Company::query()->find($company_id);
        if ($company) {
            return BaseResponse::customResponse(
                'Get data successfully',
                (new CompanyAdminTransformer)->transform(($company)),
                true,
                200,
                200,
                'Success'
            );
        }
        return BaseResponse::customResponse(
            'Data not found',
            [],
            false,
            Config('error_constant.article.not_found'),
            404,
            'Not Found'
        );
    }
}
