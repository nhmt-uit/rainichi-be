<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 4/2/19
 * Time: 10:28
 */

namespace App\Http\Controllers\API\Permission;


use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\RolePermission;
use App\Service\BaseResponse;
use Illuminate\Support\Facades\Route;

class ListController extends Controller
{
    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|null
     */
    public static function getUserPermission($user_role)
    {
        $list_permission_id = RolePermission::query()->where('role_id', $user_role)->pluck('permission_id');
        if ($list_permission_id) {
            $list_permission = Permission::query()->whereIn('id', $list_permission_id)->get();
            return $list_permission;
        }
        return null;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function importPermission()
    {
        $routeCollection = Route::getRoutes();

        $permissions = [];
        foreach ($routeCollection as $route) {
            if (explode('/', $route->uri())[0] == 'api') {
                $permission = [
                    'url' => $route->uri(),
                    'fe_url' => null,
                    'name' => null,
                    'parent_id' => null,
                    'free_access' => false,
                    'method' => $route->methods()[0]
                ];
                array_push($permissions, $permission);
            }
        }
        RolePermission::query()->truncate();
        Permission::query()->delete();
        Permission::query()->insert($permissions);

        return BaseResponse::customResponse(
            'Import success',
            [],
            true,
            200,
            201,
            'Created'
        );
    }
}