<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TransactionController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//一覧と詳細は未ログインで参照可、検索も動く
Route::get('/', [ItemController::class, 'index'])->name('index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

//検索
Route::get('/search', [App\Http\Controllers\ItemController::class, 'search'])->name('item.search');


//ログアウト_認証済みでないとログアウトできなくなるのでverifiedから外す
Route::middleware('auth')->group(function () {
    Route::post('/logout', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});


Route::middleware(['auth', 'verified'])->group(function () {

    //コメント
    Route::post('/item/{itemId}/comment', [ItemController::class, 'storeComment'])->name('item.comment.store');

    //いいね
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'toggle'])->name('item.favorite.toggle');

    //プロフィール関連
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile');

    //出品
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');

    //購入
    Route::get('/purchase/{item}', [PurchaseController::class, 'showPurchaseForm'])->name('purchase.showPurchaseForm');
    Route::post('/purchase/{item}', [PurchaseController::class, 'store'])->name('purchase.store');

    //決済
    Route::post('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

    //住所関連
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.editAddress');
    Route::post('/purchase/address/update', [PurchaseController::class, 'updateAddress'])->name('purchase.updateAddress');

    // ========================================
    // 取引チャット関連（修正版）
    // ========================================

    // 取引チャット画面
    Route::get('/transactions/{item}', [TransactionController::class, 'show'])
        ->name('transactions.show');

    // メッセージ投稿
    Route::post('/transactions/{item}/messages', [TransactionController::class, 'store'])
        ->name('transactions.store');

    // メッセージ編集（修正）
    Route::put('/transactions/{item}/messages/{message}', [TransactionController::class, 'update'])
        ->name('transactions.messages.update');

    // メッセージ削除（修正）
    Route::delete('/transactions/{item}/messages/{message}', [TransactionController::class, 'destroy'])
        ->name('transactions.messages.destroy');

    // 取引完了・評価
    Route::post('/transactions/{item}/complete', [TransactionController::class, 'complete'])
        ->name('transactions.complete');
});
