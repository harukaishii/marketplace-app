<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PurchaseHistory;
use App\Models\TransactionMessage;
use App\Models\Rating;
use App\Models\User;
use App\Mail\TransactionCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\ItemStatus;
use App\Http\Requests\TransactionMessageRequest;
use App\Http\Requests\TransactionMessageUpdateRequest;
use App\Http\Requests\RatingRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Exception;

class TransactionController extends Controller
{
    /**
     * 取引チャット画面を表示
     *
     * @param Item $item
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(Item $item)
    {
        $user = Auth::user();

        // 取引中または売却済みステータスのチェック
        if (!$item->isInTransaction() && $item->status !== ItemStatus::SOLD) {
            return redirect()->route('profile', ['page' => 'transaction'])
                ->with('error', 'この商品は取引中ではありません。');
        }

        // 購入履歴を取得
        $purchase = $item->purchase;

        if (!$purchase) {
            return redirect()->route('profile', ['page' => 'transaction'])
                ->with('error', '購入履歴が見つかりません。');
        }

        // アクセス権限のチェック（出品者または購入者のみ）
        $isSeller = ($item->listed_by === $user->id);
        $isBuyer = ($purchase->user_id === $user->id);

        if (!$isSeller && !$isBuyer) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // 取引相手を取得
        $otherUser = $isSeller ? $purchase->user : $item->seller;

        // メッセージを取得（古い順）
        $messages = TransactionMessage::where('item_id', $item->id)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // 未読メッセージを既読にする
        TransactionMessage::markAsRead($item->id, $user->id);

        // サイドバー用：取引中の商品一覧（最新メッセージ順）
        $transactionItems = $user->getTransactionItems();

        // 評価状況をチェック
        $buyerRated = false;
        $sellerRated = false;

        // 購入者の評価をチェック
        $buyerRating = Rating::where('item_id', $item->id)
            ->where('from_user_id', $purchase->user_id)
            ->where('to_user_id', $item->listed_by)
            ->first();
        $buyerRated = $buyerRating !== null;

        // 出品者の評価をチェック
        $sellerRating = Rating::where('item_id', $item->id)
            ->where('from_user_id', $item->listed_by)
            ->where('to_user_id', $purchase->user_id)
            ->first();
        $sellerRated = $sellerRating !== null;

        // 評価ボタンを表示する条件（修正）
        $canRate = false;
        $shouldAutoOpenModal = false; 

        if ($item->isInTransaction()) {
            if ($isBuyer && !$buyerRated) {
                // 購入者で未評価 → ボタン表示
                $canRate = true;
            } elseif ($isSeller && !$sellerRated && $buyerRated) {
                // 出品者で未評価 かつ 購入者が評価済み → 自動でモーダルを開く
                $shouldAutoOpenModal = true;
            }
        }

        // 評価状況を追加
        $hasRated = $isBuyer ? $buyerRated : $sellerRated;
        $buyerHasRated = $buyerRated;

        return view('transactions.show', compact(
            'item',
            'messages',
            'otherUser',
            'isSeller',
            'isBuyer',
            'transactionItems',
            'user',
            'canRate',
            'hasRated',
            'buyerHasRated',
            'shouldAutoOpenModal' // 追加
        ));
    }

    /**
     * メッセージを投稿
     *
     * @param TransactionMessageRequest $request
     * @param Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TransactionMessageRequest $request, Item $item)
    {
        $user = Auth::user();

        // 取引中ステータスのチェック
        if (!$item->isInTransaction()) {
            return redirect()->route('transactions.show', $item)
                ->with('error', 'この商品は取引中ではありません。');
        }

        // 購入履歴を取得
        $purchase = $item->purchase;

        if (!$purchase) {
            return redirect()->route('transactions.show', $item)
                ->with('error', '購入履歴が見つかりません。');
        }

        // アクセス権限のチェック
        $isSeller = ($item->listed_by === $user->id);
        $isBuyer = ($purchase->user_id === $user->id);

        if (!$isSeller && !$isBuyer) {
            abort(403, 'この取引にアクセスする権限がありません。');
        }

        // 画像のアップロード処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('transaction_images', 'public');
        }

        // メッセージを保存
        TransactionMessage::create([
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => $request->content,
            'image' => $imagePath,
        ]);

        return redirect()->route('transactions.show', $item)
            ->with('success', 'メッセージを送信しました。');
    }

    /**
     * メッセージを更新
     *
     * @param TransactionMessageUpdateRequest $request
     * @param Item $item
     * @param TransactionMessage $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(TransactionMessageUpdateRequest $request, Item $item, TransactionMessage $message)
    {
        $user = Auth::user();

        // メッセージの所有者チェック
        if ($message->user_id !== $user->id) {
            abort(403, 'このメッセージを編集する権限がありません。');
        }

        // 画像の更新処理
        if ($request->hasFile('image')) {
            // 古い画像を削除
            if ($message->image) {
                Storage::disk('public')->delete($message->image);
            }

            // 新しい画像を保存
            $imagePath = $request->file('image')->store('transaction_images', 'public');
            $message->image = $imagePath;
        } elseif ($request->input('remove_image') === '1' && $message->image) {
            // 画像削除フラグがある場合、画像を削除
            Storage::disk('public')->delete($message->image);
            $message->image = null;
        }

        // メッセージ内容を更新
        $message->content = $request->content;
        $message->save();

        return redirect()->route('transactions.show', $item)
            ->with('success', 'メッセージを更新しました。');
    }

    /**
     * メッセージを削除
     *
     * @param Item $item
     * @param TransactionMessage $message
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Item $item, TransactionMessage $message)
    {
        $user = Auth::user();

        // メッセージの所有者チェック
        if ($message->user_id !== $user->id) {
            abort(403, 'このメッセージを削除する権限がありません。');
        }

        // 画像があれば削除
        if ($message->image) {
            Storage::disk('public')->delete($message->image);
        }

        // メッセージをソフトデリート
        $message->delete();

        return redirect()->route('transactions.show', $item)
            ->with('success', 'メッセージを削除しました。');
    }

    /**
     * 取引を完了して評価
     *
     * @param RatingRequest $request
     * @param Item $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function complete(RatingRequest $request, Item $item)
    {
        $user = Auth::user();

        // 取引中または売却済みステータスのチェック
        if (!$item->isInTransaction() && $item->status !== ItemStatus::SOLD) {
            return redirect()->route('transactions.show', $item)
                ->with('error', 'この商品は取引対象ではありません。');
        }

        // 購入履歴を取得
        $purchase = $item->purchase;

        if (!$purchase) {
            return redirect()->route('transactions.show', $item)
                ->with('error', '購入履歴が見つかりません。');
        }

        // 購入者または出品者であることを確認
        $isBuyer = ($purchase->user_id === $user->id);
        $isSeller = ($item->listed_by === $user->id);

        if (!$isBuyer && !$isSeller) {
            abort(403, 'この取引の評価を行う権限がありません。');
        }

        // 評価対象者を決定
        $toUserId = $isBuyer ? $item->listed_by : $purchase->user_id;

        // 既に評価済みかチェック
        $existingRating = Rating::where('item_id', $item->id)
            ->where('from_user_id', $user->id)
            ->where('to_user_id', $toUserId)
            ->first();

        if ($existingRating) {
            return redirect()->route('transactions.show', $item)
                ->with('error', 'この取引は既に評価済みです。');
        }

        DB::beginTransaction();

        try {
            // 評価を保存
            Rating::create([
                'item_id' => $item->id,
                'from_user_id' => $user->id,
                'to_user_id' => $toUserId,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            $buyer = User::find($purchase->user_id);
            $seller = User::find($item->listed_by);

            if ($isBuyer && $seller) {
                // 購入者が評価 → 出品者にメール送信
                Mail::to($seller->email)->send(new TransactionCompleted($item, $buyer));
            } elseif ($isSeller && $buyer) {
                // 出品者が評価 → 購入者にメール送信
                Mail::to($buyer->email)->send(new TransactionCompleted($item, $seller));
            }

            // 相手が既に評価済みかチェック
            $otherUserRating = Rating::where('item_id', $item->id)
                ->where('from_user_id', $toUserId)
                ->where('to_user_id', $user->id)
                ->first();

            // 両方が評価済みの場合のみ、ステータスをSOLDに変更
            if ($otherUserRating && $item->isInTransaction()) {
                $item->status = ItemStatus::SOLD;
                $item->save();
            }

            DB::commit();

            // リダイレクト先を分岐
            if ($otherUserRating) {
                // 両方評価済み → 取引完了
                return redirect('/')
                    ->with('success', '評価を送信しました。取引が完了しました。ご利用ありがとうございました。');
            } else {
                // 相手の評価待ち → 取引画面に戻る
                return redirect()->route('transactions.show', $item)
                    ->with('success', '評価を送信しました。取引相手の評価をお待ちください。');
            }
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('transactions.show', $item)
                ->with('error', '評価の送信中にエラーが発生しました。');
        }
    }
}
