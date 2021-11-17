<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null)
    {
     switch($guard){
        case 'admin':
        if (Auth::guard('admin')->check()) {
            return redirect('/admin');
        }
        break;
        default:
        if (Auth::guard($guard)->check()) {
            return redirect('/');
        }
        break;
    }

     /*if (Auth::guard('admin')->guest() || Auth::guest()) {
          return response()->json(['error' => 'Unauthorized.'], 403);
      }*/


      $response = $next($request);
      return $response; 
  }
}
