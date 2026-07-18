<?php

namespace App\Http\Controllers\API\Notification;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Service\BaseResponse;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationGroup;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use App\Transformers\NotificationTransformer;
use App\Transformers\NotificationAdminTransformer;
use App\Http\Requests\NotificationRequest;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var NotificationTransformer
     * @var NotificationAdminTransformer
     */
    private $notificationTransformer;
    private $notificationAdminTransformer;

    function __construct(Manager $fractal, NotificationTransformer $notificationTransformer, NotificationAdminTransformer $notificationAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->notificationTransformer = $notificationTransformer;
        $this->notificationAdminTransformer = $notificationAdminTransformer;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $notification = Notification::query()->with('group')->get();
        $notification = new Collection($notification, $this->notificationTransformer);
        $notification = $this->fractal->createData($notification);
        return BaseResponse::customResponse(
            'Success',
            $notification->toArray()['data'],
            true,
            200,
            200,
            'Success'
        );
    }

    /**
     * @param NotificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(NotificationRequest $request)
    {

        $data_request = $request->all();
        $data_request['created_by'] = Auth::user()->id;
        //type
        $notification_type = strtoupper($request->get('type'));
        if ($notification_type && array_key_exists($notification_type, Notification::NOTIFICATION_TYPE)) {
            $notification_type_number = Notification::NOTIFICATION_TYPE[$notification_type];

        } else {
            $notification_type_number = 1;
        }
        $template = Notification::NOTIFICATION_TEMPLATE[$notification_type];
        switch ($notification_type) {
            case "DAILY":
                $template = sprintf($template, $request->get('minute'), $request->get('hours'));
                break;
            case "WEEKLY":
                $template = sprintf($template, $request->get('minute'), $request->get('hours'), $request->get('days_of_week'));
                break;
            case "ONETIME":
                $value = Carbon::parse($request->get('execute_date'))->format('h d m');
                $template = sprintf($template, $request->get('minute'), $value);
                break;

        }
        $data_request["type"] = $notification_type_number;
        //send to group, all or list user
        $notification_send_to = strtoupper($request->get('send_to'));
        if ($notification_send_to && array_key_exists($notification_send_to, Notification::NOTIFICATION_SEND_TO)) {
            $notification_send_to = Notification::NOTIFICATION_SEND_TO[$notification_send_to];
        } else {
            $notification_send_to = 1;
        }
        $data_request["send_to"] = $notification_send_to;
        $notification = Notification::create($data_request);
        // get translation keys and add to translation table
        $language_keys = array_keys($data_request['translations']);
        foreach ($language_keys as $language) {
            $notification->translateOrNew($language)->title = $data_request['translations'][$language]['title'];
            $notification->translateOrNew($language)->description = $data_request['translations'][$language]['description'];
        }
        if ($notification->save()) {
            $notification_id = $notification->id;
            if ($request->has('user_ids')) {
                $list_user = explode(",", $data_request['user_ids']);
                $data = array();
                if (is_array($list_user)) {
                    foreach ($list_user as $v) {
                        $user = User::query()->find($v);
                        if ($user) {
                            array_push($data, [
                                'user_id' => $user->id,
                                'notification_id' => $notification_id
                            ]);
                        }
                    }
                }
                NotificationGroup::query()->insert($data);
            }
            // Add cronjob to Crontab file
            $id = $notification->id;
            shell_exec("{ crontab -l; echo '$template php /var/www/html/backend/artisan rainichi:push $id'; } | crontab -");
            $type = array_keys(Notification::NOTIFICATION_TYPE, $notification_type_number);
            $notification->type = $type[0];
            $send_to = array_keys(Notification::NOTIFICATION_SEND_TO, $notification_send_to);
            $notification->send_to = $send_to[0];
            return BaseResponse::customResponse(
                'Create successfully',
                $notification,
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
            $notification = Notification::find($request->route('id'));
            if ($notification) {
                $notification_data = $request->all();
                //type
                $notification_type = strtoupper($request->get('type'));
                if ($notification_type !== $notification->type) {
                    $notification_data['type'] = Notification::notification_type($notification_type);
                    $template = '';
                    switch ($notification_type) {
                        case "DAILY":
                            $template = sprintf($template, $request->get('hours'));
                            break;
                        case "WEEKLY":
                            $template = sprintf($template, $request->get('hours'), $request->get('days_of_week'));
                            break;
                        case "ONETIME":
                            $value = Carbon::parse($request->get('execute_date'))->format('h d m');
                            $template = sprintf($template, $value);
                            break;
                    }
                    if ($template) {
                        $id = $notification->id;
                        // Delete first
                        shell_exec("sudo crontab -l | grep -v 'php /var/www/html/backend/artisan rainichi:push $id' | crontab -");
                        // Then add again
                        shell_exec("{ crontab -l; echo '$template php /var/www/html/backend/artisan rainichi:push $id'; } | crontab -");
                    }
                }

                $type = Notification::getNotificationType($notification_data['type']);

                //send to
                $notification_send_to = strtoupper($request->get('send_to'));
                $notification_data['send_to'] = Notification::notification_send_to($notification_send_to);
                $send_to = Notification::getNotificationSendTo($notification_data['send_to']);
                $notification->update($notification_data);
                // get translation keys and add to translation table
                if ($request->translations) {
                    $language_keys = array_keys($notification_data['translations']);
                    foreach ($language_keys as $language) {
                        $notification->translateOrNew($language)->title = $notification_data['translations'][$language]['title'];
                        $notification->translateOrNew($language)->description = $notification_data['translations'][$language]['description'];
                    }
                }
                if ($notification->save()) {
                    $notification_id = $notification->id;
                    $notification_group = NotificationGroup::where('notification_id', $notification_id);
                    if ($notification_group) {
                        $notification_group->delete();
                    }
                    if ($request->has('user_ids')) {
                        $list_user = explode(",", $notification_data['user_ids']);
                        $data = array();
                        if (is_array($list_user)) {
                            foreach ($list_user as $v) {
                                $user = User::query()->find($v);
                                if ($user) {
                                    array_push($data, [
                                        'user_id' => $user->id,
                                        'notification_id' => $notification_id
                                    ]);
                                }
                            }
                        }
                        NotificationGroup::query()->insert($data);
                    }

                    $notification->type = $type;
                    $notification->send_to = $send_to;
                    return BaseResponse::customResponse(
                        'Update successfully',
                        $notification,
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
                    'Notification not found',
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
                $notification = Notification::find($id);
                if ($notification) {
                    $notification->delete();
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
     * Get list notification with filter and transformer data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListNotificationAdmin(Request $request)
    {
        if (Auth::user()->isAdmin()) {
            //get query string
            $search_string = $request->query('search_string');
            $lang = $request->query('lang');
            $column = $request->query('column');
            $paging = $request->query('per_page') ? $request->query('per_page') : 15;
            // Get list with filter
            $data_list = Notification::searchString($search_string, $lang)->orderBy('id', 'desc')->paginate($paging)->appends($request->query());
            $data = new Collection($data_list, $this->notificationAdminTransformer);
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
            $notification = Notification::where('id', $id)->get()->first();
            if ($notification) {
                $notification = new Item($notification, $this->notificationAdminTransformer);
                $notification = $this->fractal->createData($notification);
                return BaseResponse::customResponse(
                    'Get notification by id successfully',
                    $notification->toArray()['data'],
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
