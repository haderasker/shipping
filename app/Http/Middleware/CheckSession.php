<?php

namespace App\Http\Middleware;

use Closure;

class CheckSession
{
    public function handle($request, Closure $next)
    {

        if (!session()->has('login_info') ||
            empty(session('login_info')->user_id)){
            return redirect('/');
        }

        $controller = $request->route('controller', null);

        if (empty( $controller))
            return redirect('/');

        if ( empty(session('login_info')->user_id))
            return redirect('/');

        return $next($request);
    }
}
