<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfProfileIncomplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $routeName = optional($request->route())->getName();

            // プロフィール未完成かつアクセスルートが「profile.edit」「logout」でなければリダイレクト
            if (
                !$user->profile_completed &&
                !in_array($request->route()?->getName(), ['profile.edit', 'profile.update', 'logout'])
            ) {
                return redirect()->route('profile.edit')->with('warning', 'プロフィール情報を入力してください。');
            }

            return $next($request);
    }
}
}
