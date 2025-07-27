<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\UserAddress;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        $page = $request->query('page', 'sell');

        $items = collect();

        if ($page === 'buy') {
            $items = $user->purchasedItems;
        } else {
            $items = $user->listedItems;
        }

        return view('profile.mypage', compact('user', 'items', 'page'));
    }


    public function edit()
    {
        $user = Auth::user();
        $user->load('address');
        return view('profile.edit', ['user' => $user]);
    }


    public function update(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'image_file' => 'nullable|image|max:2048',
            'post' => 'nullable|string|max:8',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $userData = [
            'name' => $request->name,
            'profile_completed' => true,
        ];

        if ($request->hasFile('image_file')) {
            if ($user->image) {
                Storage::delete($user->image);
            }
            $imagePath = $request->file('image_file')->store('users', 'public');
            $userData['image'] = $imagePath;
        }

        $user->update($userData);

        if ($user->address()) {
            $user->address->update([
                'post' => $request->post,
                'address' => $request->address,
                'building' => $request->building,
            ]);
        } else {
            $user->address()->create([
                'post' => $request->post,
                'address' => $request->address,
                'building' => $request->building,
            ]);
        }

        return redirect()->route('index')->with('status', 'プロフィールが更新されました。');
    }
}
