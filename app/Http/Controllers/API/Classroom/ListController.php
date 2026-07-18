<?php

namespace App\Http\Controllers\API\Classroom;

use App\Models\Classroom;
use App\Models\User;
use App\Models\UserClass;
use App\Service\BaseResponse;
use App\Transformers\ClassroomAdminTransformer;
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
     * @var ClassroomAdminTransformer
     */
    private $classroomAdminTransformer;

    function __construct(Manager $fractal, ClassroomAdminTransformer $classroomAdminTransformer, UserTransformer $userTransformer)
    {
        $this->fractal = $fractal;
        $this->classroomAdminTransformer = $classroomAdminTransformer;
        $this->userTransformer = $userTransformer;
    }

    /**
     * Get all classroom base on user_type: ADMIN & LEADER
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAdmin(Request $request)
    {
        if (Auth::user()->type === User::TEACHER) {
            return $this->getListByTeacher($request);
        } else {
            // This case will handle filter classs by current user
            $company_ids = Auth::user()->type === User::LEADER ? Auth::user()->userCompany->pluck('company_id') : [];
            $paging = $request->query('per_page') ? $request->query('per_page') : 15;
            $created_by = $request->query('created_by');
            $column = $request->query('column') ?? 'id';
            $order_by_type = $request->query('order_by_type') ?? 'desc';
            $is_active = $request->get('is_active');
            $is_approved = $request->get('is_approved');
            $search_string = $request->query('search_string');
            $company_request = $request->query('company_id');
            if (isset($company_request)) {
                $company_ids = [$company_request];
            }

            $classroom_list = Classroom::with('user', 'level')
                ->filterCompany($company_ids)
                ->searchName($search_string)
                ->searchByCreatedBy($created_by)
                ->orderByCustom($column, $order_by_type)
                ->isActive($is_active)
                ->isApproved($is_approved)
                ->paginate($paging);

            $classroom = new Collection($classroom_list->items(), $this->classroomAdminTransformer);
            $classroom->setPaginator(new IlluminatePaginatorAdapter($classroom_list));
            $classroom = $this->fractal->createData($classroom);
            return BaseResponse::customResponse(
                'Get list classroom successfully',
                $classroom->toArray()['data'],
                true,
                200,
                200,
                'Success',
                $classroom->toArray()['meta']
            );
        }
    }

    public function getListByTeacher(Request $request)
    {
        // This case will handle filter classs by current user
        $user_id = Auth::user()->id;
        $class_ids = UserClass::query()->where('user_id', $user_id)->pluck('classroom_id');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $column = $request->query('column') ?? 'id';
        $order_by_type = $request->query('order_by_type') ?? 'desc';
        $is_active = $request->get('is_active');
        $is_approved = $request->get('is_approved');
        $search_string = $request->query('search_string');
        $company_request = $request->query('company_id');
        $company_ids = [];
        if (isset($company_request)) {
            $company_ids = [$company_request];
        }
        $classroom_list = Classroom::with('user', 'level')
            ->whereIn('id', $class_ids)
            ->filterCompany($company_ids)
            ->searchName($search_string)
            ->searchByCreatedBy($created_by)
            ->orderByCustom($column, $order_by_type)
            ->isActive($is_active)
            ->isApproved($is_approved)
            ->paginate($paging);

        $classroom = new Collection($classroom_list->items(), $this->classroomAdminTransformer);
        $classroom->setPaginator(new IlluminatePaginatorAdapter($classroom_list));
        $classroom = $this->fractal->createData($classroom);
        return BaseResponse::customResponse(
            'Get list classroom successfully',
            $classroom->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $classroom->toArray()['meta']
        );
    }
}
