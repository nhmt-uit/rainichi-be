<?php

namespace App\Http\Controllers\API\UserTokenDevice;

use Illuminate\Http\Request;
use App\Service\BaseResponse;
use App\Http\Controllers\Controller;
use App\Models\UserTokenDevice;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use App\Transformers\UserTokenDeviceTransformer;
use App\Http\Requests\UserTokenDeviceRequest;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Auth;

class UserTokenDeviceController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var UserTokenDeviceTransformer
     */
    private $userTokenDeviceTransformer;

    function __construct(Manager $fractal, UserTokenDeviceTransformer $userTokenDeviceTransformer)
    {
        $this->fractal = $fractal;
        $this->userTokenDeviceTransformer = $userTokenDeviceTransformer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = UserTokenDevice::query()->where('user_id', Auth::id())->get();
        $data = new Collection($data, $this->userTokenDeviceTransformer);        
        $data = $this->fractal->createData($data);
        return BaseResponse::customResponse(
            'Success',
            $data->toArray()['data'],
            true,
            200,
            200,
            'Success'
        );
    }
    /**
     * @param UserTokenDeviceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(UserTokenDeviceRequest $request)
    {
        $is_device = 0;
        $device_type = $request->get('device_type');
        if ($device_type && array_key_exists($device_type, UserTokenDevice::DEVICE_TYPE)) {
            $is_device = UserTokenDevice::DEVICE_TYPE[$device_type];
        } else {
            $device_type = null;
        }
        $user_token_device_data = $request->all();
        $check_token_user = UserTokenDevice::where('token_device', $user_token_device_data['token_device'])->where('type', $is_device)->get();       
        if (count($check_token_user) == 0) {
            $user_token_device_data['type'] = $is_device;
            $user_token_device = UserTokenDevice::create($user_token_device_data);
            $user_token_device->user_id = $user_token_device_data['user_id'];
            $user_token_device->token_device = $user_token_device_data['token_device'];
            $user_token_device->type = $is_device; 
            if ($user_token_device) {
                $user_token_device->type = $device_type;
                return BaseResponse::customResponse(
                    'Create successfully',
                    $user_token_device,
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
                    Config('error_constant.user_token_device.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Token user exist',
                [],
                false,
                Config('error_constant.user_token_device.insert_fail'),
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
        $request->validate([
            'token_device' => 'required|string',
            'token_device_old' => 'required',
            'device_type' => 'required|string'
        ]);
        $is_device = 0;
        $device_type = $request->get('device_type');
        if ($device_type && array_key_exists($device_type, UserTokenDevice::DEVICE_TYPE)) {
            $is_device = UserTokenDevice::DEVICE_TYPE[$device_type];
        }        
        $user_id = Auth::user()->id;
        $user_token_device = UserTokenDevice::where('token_device', $request->get('token_device_old'))
        ->where('type', $is_device)
        ->where('user_id', $user_id)->first();        
        $user_token_device_data = $request->all();
        if ($user_token_device) {            
            $user_token_device_data['token_device'] = $user_token_device_data['token_device'];
            $user_token_device->update($user_token_device_data);
            if ($user_token_device->save()) {
                $user_token_device->type = $device_type;
                return BaseResponse::customResponse(
                    'Update successfully',
                    $user_token_device,
                    true,
                    200,
                    202,
                    'Accepted'
                );
            } else {
                return BaseResponse::customResponse(
                    'Fail to Update',
                    [],
                    false,
                    Config('error_constant.user_token_device.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
        } else {
            $check_token_user = UserTokenDevice::where('token_device', $user_token_device_data['token_device'])->where('type', $is_device)->get();       
            if (count($check_token_user) == 0) { 
                //add new
                $user_token_device_data['type'] = $is_device;
                $user_token_device_data['user_id'] = $user_id;
                $user_token_device = UserTokenDevice::create($user_token_device_data);
                $user_token_device->token_device = $user_token_device_data['token_device'];
                $user_token_device->type = $is_device; 
                if ($user_token_device) {
                    $user_token_device->type = $device_type;
                    return BaseResponse::customResponse(
                        'Create successfully',
                        $user_token_device,
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
                        Config('error_constant.user_token_device.insert_fail'),
                        422,
                        'Unprocessable Entity'
                    );
                }
            } else {
                return BaseResponse::customResponse(
                    'Token user exist',
                    [],
                    false,
                    Config('error_constant.user_token_device.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
            
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $request->validate([
            'token_device' => 'required|string',
            'device_type' => 'required|string'
        ]);  
        $is_device = 1;
        $device_type = $request->get('device_type');
        if ($device_type && array_key_exists($device_type, UserTokenDevice::DEVICE_TYPE)) {
            $is_device = UserTokenDevice::DEVICE_TYPE[$device_type];
        }
        $user_id = Auth::user()->id;        
        $user_token_device = UserTokenDevice::where('token_device', $request->get('token_device'))
        ->where('type', $is_device)
        ->where('user_id', $user_id);        
        if (count($user_token_device->get()) > 0) {
            $user_token_device->delete();
            return BaseResponse::customResponse(
                'Delete successfully',
                [],
                true,
                200,
                202,
                'Accepted'
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
     * Get detail by id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $data = UserTokenDevice::where('id', $id)->first();
        if ($data) {
            $data = new Item($data, $this->userTokenDeviceTransformer);
            $data = $this->fractal->createData($data);
            return BaseResponse::customResponse(
                'Get user token device by id successfully',
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
     * Get detail by token device
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail_by_token_device(Request $request)
    {
        $data = UserTokenDevice::where('token_device', $request->get('token_device'))->first();      
        if ($data) {
            $data = new Item($data, $this->userTokenDeviceTransformer);
            $data = $this->fractal->createData($data);
            return BaseResponse::customResponse(
                'Get user token device by id successfully',
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
}
