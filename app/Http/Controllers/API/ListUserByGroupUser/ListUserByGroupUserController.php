<?php

namespace App\Http\Controllers\API\ListUserByGroupUser;

use Illuminate\Http\Request;
use App\Service\BaseResponse;
use App\Http\Controllers\Controller;
use App\Models\ListUserByGroupUser;
use App\Models\GroupUser;
use App\Models\User;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use App\Transformers\ListUserByGroupUserTransformer;
use App\Transformers\ListUserByGroupUserAdminTransformer;
use App\Http\Requests\ListUserByGroupUserRequest;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Auth;

class ListUserByGroupUserController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var ListUserByGroupUserTransformer
     * @var ListUserByGroupUserAdminTransformer
     */
    private $listUserByGroupUserTransformer;
    private $listUserByGroupUserAdminTransformer;

    function __construct(Manager $fractal, ListUserByGroupUserTransformer $listUserByGroupUserTransformer, ListUserByGroupUserAdminTransformer $listUserByGroupUserAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->listUserByGroupUserTransformer = $listUserByGroupUserTransformer;
        $this->listUserByGroupUserAdminTransformer = $listUserByGroupUserAdminTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $listUserByGroupUser = ListUserByGroupUser::query()->get();        
        $listUserByGroupUser = new Collection($listUserByGroupUser, $this->listUserByGroupUserTransformer);        
        $listUserByGroupUser = $this->fractal->createData($listUserByGroupUser);
        return BaseResponse::customResponse(
            'Success',
            $listUserByGroupUser->toArray()['data'],
            true,
            200,
            200,
            'Success'
        );
    }
    /**
     * @param ListUserByGroupUser $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(ListUserByGroupUserRequest $request)
    {        
        $data_request = $request->all();
        if ($request->has('list_user') && $request->has('group_user_id') ) {
            $list_user = $data_request['list_user'];
            $group_user_id = $data_request['group_user_id'];    
            $check_group_user = GroupUser::query()->find($group_user_id);    
            if ($check_group_user) {
                $data = array();
                if (is_array($list_user)) {
                    foreach ($list_user as $v) {
                        $user = User::query()->find($v);
                        if ($user) {
                            $check_group_user_exist = ListUserByGroupUser::where('group_user_id',$group_user_id)->where('user_id', $v);   
                            if ($check_group_user_exist->count() == 0) {
                                array_push($data, [
                                    'user_id' => $user->id,
                                    'group_user_id' => $group_user_id
                                ]);
                            }
                            
                        }
                    }
                }             
                $insert = ListUserByGroupUser::query()->insert($data);
                if ($insert) {
                    return BaseResponse::customResponse(
                        'Create successfully',
                        [],
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
            } else {
                return BaseResponse::customResponse(
                    'Group user not found',
                    [],
                    false,
                    404,
                    404,
                    'NotFound'
                );
            }  
            
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
        if ($request->isMethod('patch')) 
        {
            $groupUser = GroupUser::find($request->route('id'));
            if ($groupUser) {
                $group_user_data = $request->all();   
                $list_user = $group_user_data['list_user'];     
                $groupUser->update($group_user_data);                
                if ($groupUser->save() && $list_user) {                
                    $group_user_id = $groupUser->id;                    
                    $list_user_group = ListUserByGroupUser::where('group_user_id', $group_user_id)->whereIn('user_id', $list_user);
                    if ($list_user_group) {
                        $list_user_group->delete();
                    }
                    return BaseResponse::customResponse(
                        'Update successfully',
                        $list_user_group,
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
            $data_list = ListUserByGroupUser::searchString($search_string)->orderBy('id', 'desc')->paginate($paging)->appends($request->query());
            $data = new Collection($data_list, $this->listUserByGroupUserAdminTransformer);
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
    public function detailForAdmin(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            $userData = [
                'data' => [],
                'meta' => []
            ];
            $id = strtoupper($request->route('id'));
            $paging = $request->query('per_page') ? $request->query('per_page') : 15;
            // Get list with filter
            $data_list = ListUserByGroupUser::where('group_user_id', $id)->orderBy('id', 'desc')->paginate($paging)->appends($request->query());
            $data = new Collection($data_list, $this->listUserByGroupUserAdminTransformer);
            $data->setPaginator(new IlluminatePaginatorAdapter($data_list));
            $data = $this->fractal->createData($data);
            $userData['data'] = $data->toArray()['data'];
            $userData['meta'] = $data->toArray()['meta'];
            if ($data) {
                // Response to json
                return BaseResponse::customResponse(
                    'Success',
                    $userData['data'],
                    true,
                    200, 200,
                    'Success',
                    $userData['meta']
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

