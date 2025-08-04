<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Item;
use App\Models\UserAddress;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ProfileRequest;

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

        // 1. ProfileRequestのバリデーションルールとメッセージを取得
        $profileRules = (new ProfileRequest)->rules();
        $profileMessages = (new ProfileRequest)->messages();

        // 2. AddressRequestのバリデーションルールとメッセージを取得
        $addressRules = (new AddressRequest)->rules();
        $addressMessages = (new AddressRequest)->messages();

        // 3. 全てのバリデーションルールとメッセージを結合
        $rules = array_merge($profileRules, $addressRules);
        $messages = array_merge($profileMessages, $addressMessages);

        // 4. 全ての入力に対してバリデーションを実行
        $validator = Validator::make($request->all(), $rules, $messages);

        // 5. バリデーションに失敗した場合
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator) // 全てのエラーをまとめてビューに渡す
                ->withInput();
        }


        // ユーザーデータの更新
        $userData = [
            'name' => $request->name, // AddressRequestでnameをバリデーションしているので、ここから取得
            'profile_completed' => true,
        ];

        // 画像の処理
        if ($request->hasFile('image_file')) {
            // 古い画像がある場合は削除 (publicディスクから削除)
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            // 新しい画像を保存 (publicディスクの'users'フォルダに保存)
            $imagePath = $request->file('image_file')->store('users', 'public');
            $userData['image'] = $imagePath; // DBには'users/xxxx.png'のようなパスを保存
        } else {
            // 画像が選択されなかった場合で、かつ既存の画像がある場合は、既存の画像を保持
            // ProfileRequestが'required'なので、このelseブロックには入らないはずですが念のため
            if ($user->image && !isset($userData['image'])) {
                $userData['image'] = $user->image;
            }
        }

        $user->update($userData);

        // 住所情報の更新
        $user->address()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'post' => $request->post,
                'address' => $request->address,
                'building' => $request->building,
            ]
        );

        return redirect()->route('index')->with('status', 'プロフィールが更新されました。');
    }
}
