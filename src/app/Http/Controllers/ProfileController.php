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
use App\Enums\ItemStatus;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        $page = $request->query('page', 'sell');

        // 未読メッセージの総数を取得（全タブで表示するため）
        $totalUnreadCount = $user->getTotalUnreadCount();

        $items = collect();

        if ($page === 'buy') {
            // 購入した商品（売却済みのみ）
            $items = $user->purchasedItems()
                ->where('status', ItemStatus::SOLD->value)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($page === 'transaction') {
            // 取引中の商品（購入 + 出品）
            // 最新メッセージ順に自動ソート済み
            $items = $user->getTransactionItems();
        } else {
            // 出品した商品（販売中 + 売却済み）
            $items = $user->listedItems()
                ->whereIn('status', [ItemStatus::AVAILABLE->value, ItemStatus::SOLD->value])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('profile.mypage', compact('user', 'items', 'page', 'totalUnreadCount'));
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

        //ProfileRequestのバリデーションルールとメッセージを取得
        $profileRules = (new ProfileRequest)->rules();
        $profileMessages = (new ProfileRequest)->messages();

        //AddressRequestのバリデーションルールとメッセージを取得
        $addressRules = (new AddressRequest)->rules();
        $addressMessages = (new AddressRequest)->messages();

        //全てのバリデーションルールとメッセージを結合
        $rules = array_merge($profileRules, $addressRules);
        $messages = array_merge($profileMessages, $addressMessages);

        //全ての入力に対してバリデーションを実行
        $validator = Validator::make($request->all(), $rules, $messages);

        //バリデーションに失敗した場合
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator) // 全てのエラーをまとめてビューに渡す
                ->withInput();
        }


        //ユーザーデータの更新
        $userData = [
            'name' => $request->name, // AddressRequestでnameをバリデーションしているので、ここから取得
            'profile_completed' => true,
        ];

        //画像の処理
        if ($request->hasFile('image_file')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            // 新しい画像を保存 (publicディスクの'users'フォルダに保存)
            $imagePath = $request->file('image_file')->store('users', 'public');
            $userData['image'] = $imagePath;
        } else {
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

        return redirect()->route('profile.edit')->with('status', 'プロフィールが更新されました。');
    }
}
