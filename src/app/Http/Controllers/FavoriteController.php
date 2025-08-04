<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * いいねの追加・削除を切り替える
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggle(Item $item)
    {
        $user = Auth::user();

        // 既にいいねしているか確認
        $favorite = Favorite::where('user_id', $user->id)
            ->where('item_id', $item->id)
            ->first();

        if ($favorite) {
            // いいね済みであれば削除
            $favorite->delete();
            $isFavorited = false;
        } else {
            // いいねしていなければ追加
            Favorite::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
            ]);
            $isFavorited = true;
        }

        // いいね数の更新
        $favoriteCount = $item->favorites->count();

        return response()->json([
            'isFavorited' => $isFavorited,
            'favoriteCount' => $favoriteCount,
        ]);
    }
}
