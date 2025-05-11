<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $uri = request()->segments();
        $user = Auth()->user();
        $rolePermissions = RolePermission::where('role_id',$user->role_id)->with('permission')->get();
        foreach ($rolePermissions as $rolePermission){
            if($rolePermission->permission->controller == $uri[0] && $rolePermission->permission->action == $uri[1]){
                return $next($request);
            }
        }
        abort(401);
    }
}
