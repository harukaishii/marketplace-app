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

        \Log::info('updateAddressメソッドが呼び出されました！'); // これを追加
        \Log::info('リクエストデータ:', $request->all()); // これも追加

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
        $userAddress->building = $request->building; // 建物名がある場合

        $userAddress->save(); // データベースに保存

        \Log::info('保存前のUserAddressデータ:', $userAddress->toArray());

        try {
            $userAddress->save(); // ここで保存
            \Log::info('UserAddressが正常に保存されました。'); // これを追加
        } catch (\Exception $e) {
            \Log::error('UserAddress保存中にエラーが発生しました: ' . $e->getMessage()); // これを追加
            return back()->withErrors(['db_error' => '住所の保存中にエラーが発生しました。'])->withInput();
        }

        return redirect()->route('purchase.showPurchaseForm', ['item' => $request->item_id])->with('success', '送付先住所を更新しました！');
    }


    public function store(PurchaseRequest $request,Item $item){


        $validated = $request->validated();

        // 配送先が登録されているか確認
        $userAddress = Auth::user()->address;
        if (!$userAddress) {
            return back()->withErrors(['address' => '配送先が登録されていません。新しい住所を登録してください。']);
        }

        // 商品がすでに購入済みではないか確認
        if ($item->status === ItemStatus::SOLD) {
            return back()->withErrors(['item' => 'この商品はすでに売り切れです。']);
        }

        // トランザクションを開始
        DB::beginTransaction();

        try {
            // 2. Itemの更新
            $item->status = ItemStatus::SOLD;
            $item->save();
            \Log::debug('Request received', ['data' => $request->all()]);

            // 3. PurchaseHistoryの作成
            PurchaseHistory::create([
                'user_id' => Auth::id(),
                'item_id' => $item->id,
                'user_address_id' =>$userAddress->id,
                'payment_type' => PaymentType::from($request->input('payment_type')),
            ]);

            DB::commit(); // トランザクションをコミット

            // 4. リダイレクト
            return redirect()->route('item.show',['item_id' => $item->id])->with('success', '商品を購入しました！');

        } catch (\Exception $e) {
            DB::rollBack(); // エラーが発生した場合はロールバック
            return back()->withErrors(['purchase_error' => '購入処理中にエラーが発生しました。もう一度お試しください。'])
                ->withInput();
        }

    }




}
