<?php

namespace App\Http\Controllers\API\Company\User;

use App\Models\Classroom;
use App\Models\Company;
use App\Models\User;
use App\Models\UserCompany;
use App\Service\BaseResponse;
use App\Transformers\CompanyAdminTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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

    function __construct(Manager $fractal, UserTransformer $userTransformer)
    {
        $this->fractal = $fractal;
        $this->userTransformer = $userTransformer;
    }

    /**
     * Get List User in Company
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListCompanyAdmin(Request $request)
    {
        $userData = [
            'data' => [],
            'meta' => []
        ];
        $company_id = $request->route('id');
        $column = $request->query('column') ?? 'id';
        $order_by_type = $request->query('order_by_type') ?? 'desc';
        $search_string = $request->query('search_string');
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $company = Company::query()->find($company_id);
        if ($company) {
            $user_list = $company->users()
                ->where('users.active', true)
                ->searchBy($search_string)
                ->orderByCustom($column, $order_by_type)
                ->where('users.type', User::LEADER)
                ->paginate($per_page);
            $users = new Collection($user_list, $this->userTransformer);
            $users->setPaginator(new IlluminatePaginatorAdapter($user_list));
            $users = $this->fractal->createData($users);
            $userData['data'] = $users->toArray()['data'];
            $userData['meta'] = $users->toArray()['meta'];
        }
        return BaseResponse::customResponse(
            'Get list successfully',
            $userData['data'],
            true,
            200,
            200,
            'Success',
            $userData['meta']
        );
    }

    public function getListCompanyUser(Request $request)
    {
        $userData = [
            'data' => [],
            'meta' => []
        ];
        $company_id = $request->route('id');
        $classroom_id = $request->query('classroom_id') ?? null;
        $column = $request->query('column') ?? 'id';
        $order_by_type = $request->query('order_by_type') ?? 'desc';
        $search_string = $request->query('search_string');
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $user_list =
            User::query()
            ->join('user_company', 'user_company.user_id', '=', 'users.id')
            ->join('companies', 'companies.id', '=', 'user_company.company_id')
            ->where('companies.id', $company_id)
            ->where('user_company.is_active', true)
            ->where('companies.is_active', true)
            ->select('users.*')
            ->paginate($per_page);

        $users = new Collection($user_list, $this->userTransformer);
        $users->setPaginator(new IlluminatePaginatorAdapter($user_list));
        $users = $this->fractal->createData($users);
        $userData['data'] = $users->toArray()['data'];
        $userData['meta'] = $users->toArray()['meta'];
        return BaseResponse::customResponse(
            'Get list user successfully',
            $userData['data'],
            true,
            200,
            200,
            'Success',
            $userData['meta']
        );
    }
}
