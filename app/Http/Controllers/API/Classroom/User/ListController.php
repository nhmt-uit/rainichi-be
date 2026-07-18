<?php

namespace App\Http\Controllers\API\Classroom\User;

use App\Models\Classroom;
use App\Models\User;
use App\Models\UserClass;
use App\Service\BaseResponse;
use App\Transformers\UserClassAdminTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var UserClassAdminTransformer
     */
    private $userClassAdminTransformer;

    function __construct(Manager $fractal, UserClassAdminTransformer $userClassAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->userClassAdminTransformer = $userClassAdminTransformer;
    }

    /**
     * Get List User in Classroom
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListUserClass(Request $request)
    {
        $userData = [
            'data' => [],
            'meta' => []
        ];
        $classroom_id = $request->route('id');
        $column = $request->query('column') ?? 'id';
        $order_by_type = $request->query('order_by_type') ?? 'desc';
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $userList = UserClass::query()->with('user')
            ->whereHas('user', function ($query) {
                return $query->whereNotIn('users.type', [User::TEACHER, User::ADMIN]);
            })
            ->where('classroom_id', $classroom_id)
            ->orderByCustom($column, $order_by_type)->paginate($per_page);

        $users = new Collection($userList, $this->userClassAdminTransformer);
        $users->setPaginator(new IlluminatePaginatorAdapter($userList));
        $users = $this->fractal->createData($users);
        $userData['data'] = $users->toArray()['data'];
        $userData['meta'] = $users->toArray()['meta'];

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
}
