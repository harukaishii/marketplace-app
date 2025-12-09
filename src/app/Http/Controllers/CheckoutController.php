<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;
use App\Models\Item;
use App\Models\UserAddress;
use App\Models\PurchaseHistory;
use App\Enums\PaymentType;
use App\Enums\ItemStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class CheckoutController extends Controller
{
    // createメソッドは提供されたコードのままでOK
    public function create(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));
        $paymentTypeValue = (int)$request->input('payment_type');

        try {
            $paymentType = PaymentType::from($paymentTypeValue);
            $paymentMethodTypes = [];
            if ($paymentType === PaymentType::CONVENIENCE) {
                $paymentMethodTypes = ['konbini'];
            } elseif ($paymentType === PaymentType::CARD) {
                $paymentMethodTypes = ['card'];
            } else {
                return back()->with('error', '無効な支払い方法が選択されました。');
            }

            $itemId = $request->input('item_id');
            $item = Item::findOrFail($itemId);

            $session = $stripe->checkout->sessions->create([
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $item->name],
                        'unit_amount' => $item->price,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'payment_method_types' => $paymentMethodTypes,
                'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('checkout.cancel'),
                'metadata' => [
                    'item_id' => $itemId,
                    'payment_type' => $paymentType->value,
                    'user_id' => Auth::id(),
                ],
            ]);

            return redirect()->away($session->url);
        } catch (Exception $e) {
            return back()->with('error', '決済中にエラーが発生しました：' . $e->getMessage());
        }
    }

    // 決済成功後の処理
    public function success(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        try {
            $sessionId = $request->query('session_id');
            if (!$sessionId) {
                return redirect()->route('checkout.cancel')->with('error', '決済情報が不足しています。');
            }

            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                // Stripeのメタデータから情報を取得
                $itemId = $session->metadata->item_id;
                $paymentTypeValue = (int)$session->metadata->payment_type;
                $userId = $session->metadata->user_id;

                // データベーストランザクションを開始
                DB::beginTransaction();

                try {
                    // 商品のステータスを取引中に更新
                    $item = Item::findOrFail($itemId);
                    $item->status = ItemStatus::IN_TRANSACTION;
                    $item->save();

                    // ユーザーの住所情報を取得
                    $userAddress = UserAddress::where('user_id', $userId)->firstOrFail();

                    // 購入履歴を保存
                    PurchaseHistory::create([
                        'user_id' => $userId,
                        'item_id' => $item->id,
                        'user_address_id' => $userAddress->id,
                        'payment_type' => PaymentType::from($paymentTypeValue),
                    ]);

                    DB::commit();

                    return view('checkout.success')->with('success', '商品を購入しました！取引画面でメッセージをやり取りしてください。');
                } catch (Exception $e) {
                    DB::rollBack();
                    // エラーログの記録
                    return redirect()->route('checkout.cancel')->with('error', '購入処理中にエラーが発生しました。');
                }
            } else {
                return redirect()->route('checkout.cancel')->with('error', '決済が完了できませんでした');
            }
        } catch (Exception $e) {
            return redirect()->route('checkout.cancel')->with('error', '決済情報が取得できませんでした');
        }
    }

    public function cancel()
    {
        return view('checkout.cancel');
    }
}
