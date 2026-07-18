<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use App\Service\BaseResponse;
use Closure;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user_role = $request->user()->type;
        $current_route = $request->path();
        $permission_id = \App\Models\Permission::query()->where('url', $current_route)->first();
        if ($permission_id) {
            $hasPermission = RolePermission::query()->where('permission_id', $permission_id)->where('role_id', $user_role)->first();
            if ($hasPermission) {
                return $next($request);
            } else {
                return BaseResponse::customResponse(
                  'Permission denied',
                  [],
                  false,
                  403,
                  403,
                  'Permission denied',
                  []
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Not Found',
                [],
                false,
                404,
                404,
                'NotFound',
                []
            );
        }
    }
}
