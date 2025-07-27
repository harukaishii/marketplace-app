<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): RedirectResponse
    {

        $user = Auth::user();
        \Log::debug('RegisterResponse: user.profile_completed = ' . ($user->profile_completed ? 'true' : 'false'));

        if (!$user->profile_completed) {
            return redirect()->route('profile.edit');
        }

        return redirect('index');
    }
}
