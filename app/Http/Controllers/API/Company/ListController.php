<?php

namespace App\Http\Controllers\API\Company;

use App\Models\Classroom;
use App\Models\Company;
use App\Service\BaseResponse;
use App\Transformers\CompanyAdminTransformer;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CompanyAdminTransformer
     */
    private $companyAdminTransformer;

    function __construct(Manager $fractal, CompanyAdminTransformer $companyAdminTransformer, UserTransformer $userTransformer)
    {
        $this->fractal = $fractal;
        $this->companyAdminTransformer = $companyAdminTransformer;
        $this->userTransformer = $userTransformer;
    }

    /**
     * Get list Company admin
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAdmin(Request $request)
    {

        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $is_active = $request->get('is_active');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $companyList = Company::with('user');
        if (Auth::user()->type === User::LEADER) {
            $userCompanyIds = Auth::user()->userCompany->pluck('company_id');
            $companyList = $companyList->whereIn('id', $userCompanyIds);
        }
        $companyList = $companyList
            ->searchName($search_string)
            ->searchByCreatedBy($created_by)
            ->orderByCustom($column, $order_by_type)
            ->isActive($is_active)
            ->paginate($paging);

        $company = new Collection($companyList->items(), $this->companyAdminTransformer);
        $company->setPaginator(new IlluminatePaginatorAdapter($companyList));
        $company = $this->fractal->createData($company);
        return BaseResponse::customResponse(
            'Get list successfully',
            $company->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $company->toArray()['meta']
        );
    }


}
