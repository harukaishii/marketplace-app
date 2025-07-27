<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): RedirectResponse
    {

        $user = Auth::user();

        // セッションの intended を破棄
        $request->session()->forget('url.intended');


        // 初回ログイン（プロフィール未設定）ならプロフィール編集へ
        if (!$user->profile_completed) {
            return redirect()->route('profile.edit');
        }

        // それ以外は通常のトップページへ
        return redirect('/');
    }
}

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request)
    {
        return redirect('/login');
    }
}
