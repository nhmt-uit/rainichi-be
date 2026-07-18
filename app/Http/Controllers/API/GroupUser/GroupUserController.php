<?php

namespace App\Http\Controllers\API\GroupUser;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Service\BaseResponse;
use App\Http\Controllers\Controller;
use App\Models\GroupUser;
use App\Models\User;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use App\Transformers\GroupUserTransformer;
use App\Transformers\GroupUserAdminTransformer;
use App\Http\Requests\GroupUserRequest;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Auth;

class GroupUserController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var GroupUserTransformer
     * @var GroupUserAdminTransformer
     */
    private $groupUserTransformer;
    private $groupUserAdminTransformer;

    function __construct(Manager $fractal, GroupUserTransformer $groupUserTransformer, GroupUserAdminTransformer $groupUserAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->groupUserTransformer = $groupUserTransformer;
        $this->groupUserAdminTransformer = $groupUserAdminTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $groupUser = GroupUser::query()->get();        
        $groupUser = new Collection($groupUser, $this->groupUserTransformer);        
        $groupUser = $this->fractal->createData($groupUser);
        return BaseResponse::customResponse(
            'Success',
            $groupUser->toArray()['data'],
            true,
            200,
            200,
            'Success'
        );
    }
    /**
     * @param GroupUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(GroupUserRequest $request)
    {        
        $data_request = $request->all();
        $data_request['created_by'] = Auth::user()->id;
        $groupUser = GroupUser::create($data_request);
        if ($groupUser->save()) {
            return BaseResponse::customResponse(
                'Create successfully',
                $groupUser,
                true,
                200,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to insert',
                [],
                false,
                422,
                422,
                'Unprocessable Entity'
            );
        }
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('patch')) {
            $groupUser = GroupUser::find($request->route('id'));
            if ($groupUser) {
                $group_user_data = $request->all();                     
                $groupUser->update($group_user_data);                
                if ($groupUser->save()) {                
                    $group_user_id = $groupUser->id;
                    // $notification_group = GroupUser::where('notification_id', $notification_id);
                    // if ($notification_group) {
                    //     $notification_group->delete();
                    // }
                    return BaseResponse::customResponse(
                        'Update successfully',
                        $groupUser,
                        true,
                        200,
                        202,
                        'Accepted'
                    );
                } else {
                    return BaseResponse::customResponse(
                        'Fail to insert',
                        [],
                        false,
                        422,
                        422,
                        'Unprocessable Entity'
                    );
                }
            } else {
                BaseResponse::customResponse(
                    'Not found',
                    [],
                    false,
                    404,
                    404,
                    'NotFound'
                );
            }
        }     
    }

    /**
     * @param $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $groupUser = GroupUser::find($id);
                if ($groupUser) {
                    $groupUser->delete();
                }
            }
            return BaseResponse::customResponse(
                'Delete successfully',
                [],
                true,
                200,
                202,
                'Accepted'
            );
        }
    }
    /**
     * Get detail by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $data = Notification::where('id', $id)->first();
        if ($data) {
            $data = new Item($data, $this->notificationTransformer);
            $data = $this->fractal->createData($data);
            return BaseResponse::customResponse(
                'Get notification by id successfully',
                $data->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Not found',
                [],
                false,
                404,
                404,
                'NotFound'
            );
        }
    }
    /**
     * Get list group user with filter and transformer data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListForAdmin(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            //get query string
            $search_string = $request->query('search_string');
            $column = $request->query('column');
            $paging = $request->query('per_page') ? $request->query('per_page') : 15;
            // Get list with filter
            $data_list = GroupUser::searchString($search_string)->orderBy('id', 'desc')->paginate($paging)->appends($request->query());
            $data = new Collection($data_list, $this->groupUserAdminTransformer);
            $data->setPaginator(new IlluminatePaginatorAdapter($data_list));
            $data = $this->fractal->createData($data);
            if ($data) {
                // Response to json
                return BaseResponse::customResponse(
                    'Success',
                    $data->toArray()['data'],
                    true,
                    200, 200,
                    'Success',
                    $data->toArray()['meta']
                );
            } else {
                return BaseResponse::customResponse(
                    'Not found',
                    [],
                    false,
                    404,
                    404,
                    'NotFound'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Permission denied',
                [],
                false,
                403,
                403,
                "Permission denied",
                []
            );
        }
    }

    /**
     * Detail for admin
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailForAdmin($id)
    {
        if (Auth::user()->isAdmin()) {
            $groupUser = GroupUser::where('id', $id)->get()->first();
            if ($groupUser) {
                $groupUser = new Item($groupUser, $this->groupUserAdminTransformer);
                $groupUser = $this->fractal->createData($groupUser);
                return BaseResponse::customResponse(
                    'Get data by id successfully',
                    $groupUser->toArray()['data'],
                    true,
                    200,
                    200,
                    'Success'
                );
            } else {
                return BaseResponse::customResponse(
                    'Not found',
                    [],
                    false,
                    404,
                    404,
                    'NotFound'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Permission denied',
                [],
                false,
                403,
                403,
                "Permission denied",
                []
            );
        }
        
    }

}
