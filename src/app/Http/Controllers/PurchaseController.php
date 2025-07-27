<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function purchase($item_id){
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        $userAddress = $user->address;


        $item = Item::find($item_id);

        if (!$item) {
            abort(404, '商品が見つかりません。');
        }

        return view('purchase.purchase',compact('item','userAddress'));
    }

    public function address(){

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'ログインしてください。');
        }

        $userAddress = $user->address;

        return view('purchase.address',compact('userAddress'));
    }
}
