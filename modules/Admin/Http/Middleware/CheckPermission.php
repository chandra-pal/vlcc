<?php

namespace Modules\Admin\Http\Middleware;

use Closure;
use Auth;

class CheckPermission
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //check if user has access to current link
        if ($this->userHasAccessTo($request)) {

            //get the current route action
            $action = $request->route()->getAction();

            //get all the permissions assigned to link or related link
            $permissions = Auth::guard('admin')->user()->linkPermissions($action['as']);

            //assing the permissions to permission variables defined from traits
            Auth::guard('admin')->user()->setPermissions($permissions);

            return $next($request);
        }

        if ($request->ajax()) {
            abort(403);
        } else {
            return view('admin::errors.403');
        }
    }

    /**
     * Checks if user has access to this requested route
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Boolean true if has permission otherwise false
     */
    protected function userHasAccessTo($request)
    {
        return $this->hasPermission($request);
    }

    /**
     * hasPermission Check if user has requested route permimssion
     *
     * @param  \Illuminate\Http\Request $request
     * @return Boolean true if has permission otherwise false
     */
    protected function hasPermission($request)
    {
        return $this->requiredPermission($request);
        //return !$this->forbiddenRoute($request) && $this->requiredPermission($request);
    }

    /**
     * Extract required permission from requested route
     *
     * @param  \Illuminate\Http\Request  $request
     * @return String permission_slug connected to the Route
     */
    protected function requiredPermission($request)
    {
        $action = $request->route()->getAction();

        return Auth::guard('admin')->user()->linkPermissions($action['as'], true);
    }

    /**
     * Check if current route is hidden to current user role
     *
     * @param  \Illuminate\Http\Request $request
     * @return Boolean true/false
     */
    protected function forbiddenRoute($request)
    {
        $action = $request->route()->getAction();

        if (isset($action['except'])) {

            //  return $action['except'] == $request->user()->role->role_slug;
        }

        return false;
    }
}
