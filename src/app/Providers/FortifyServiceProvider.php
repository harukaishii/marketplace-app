<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Laravel\Fortify\Http\Requests\RegisterRequest as FortifyRegisterRequest;
use App\Http\Requests\LoginRequest as CustomLoginRequest;
use App\Http\Requests\RegisterRequest as CustomRegisterRequest;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LoginResponse as CustomLoginResponse;
use App\Http\Responses\LogoutResponse as CustomLogoutResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use App\Http\Responses\RegisterResponse as CustomRegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // カスタムフォームリクエストをバインド
        $this->app->bind(FortifyLoginRequest::class, CustomLoginRequest::class);
        $this->app->bind(FortifyRegisterRequest::class, CustomRegisterRequest::class);

    }

    public function boot(): void
    {
        // ログイン・登録ビューの指定
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));

        // ユーザー関連の処理バインド
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // ログイン後/ログアウト後のレスポンス処理をオーバーライド
        $this->app->singleton(LoginResponseContract::class, CustomLoginResponse::class);
        $this->app->singleton(LogoutResponseContract::class, CustomLogoutResponse::class);
        $this->app->singleton(RegisterResponseContract::class, CustomRegisterResponse::class);

    }
}
