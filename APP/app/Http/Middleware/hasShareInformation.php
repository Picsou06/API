<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class hasShareInformation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            $userInfo = DB::table('users_information')->where('user_id', $user->id)->first();

            if (!$userInfo) {
                return redirect()->route('registration_form');
            } elseif ($request->route()->getName() == 'registration_form') {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
