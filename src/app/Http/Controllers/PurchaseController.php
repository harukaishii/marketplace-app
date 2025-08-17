<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PurchaseHistory;
use App\Enums\ItemStatus;
use App\Enums\PaymentType;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\PurchaseRequest;
use App\Http\Requests\AddressRequest;


class PurchaseController extends Controller
{
    public function showPurchaseForm(Item $item){

        $userAddress = Auth::user()->address;
        return view('purchase.purchase', compact('item', 'userAddress'));
    }

    //住所変更を表示
    public function editAddress(Item $item)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }
        $userAddress = $user->address;

        return view('purchase.address', compact('userAddress', 'item'));
    }

    //住所を更新
    public function updateAddress(AddressRequest $request)
    {

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        $userAddress = $user->address;

        if (!$userAddress) {
            // ユーザーに紐づく住所情報がまだない場合
            $userAddress = new UserAddress();
            $userAddress->user_id = $user->id; // user_id を設定
        }

        // リクエストから送られてきたデータで住所情報を更新
        $userAddress->post = $request->post;
        $userAddress->address = $request->address;
        $userAddress->building = $request->building;

        $userAddress->save(); // データベースに保存

        return redirect()->route('purchase.showPurchaseForm', ['item' => $request->item_id])->with('success', '送付先住所を更新しました！');
    }

}
