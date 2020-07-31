<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {if (Auth::guard($guard)->check()) {

//            if(Auth::user()->hasRole('admin')){
//                return redirect(route('admin.dashboard'));
//            }

        $user = auth()->user();

        if($user->super_admin == 1) {
            return redirect(route('super-admin.dashboard'));
        }
        elseif($user->hasRole('admin')){
            return redirect(route('admin.dashboard'));
        }
        elseif($user->hasRole('employee')){
            return redirect(route('member.dashboard'));
        }
        elseif($user->hasRole('client')){
            return redirect(route('client.dashboard.index'));
        }
    }

        return $next($request);
    }
}
