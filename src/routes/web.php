<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\FavoriteController;
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
//一覧と詳細は未ログインで参照可
Route::get('/', [ItemController::class,'index'])->name('index');
Route::get('/item/{item_id}',[ItemController::class,'show'])->name('item.show');

//検索
Route::get('/search', [App\Http\Controllers\ItemController::class, 'search'])->name('item.search');


Route::middleware(['auth'])->group(function(){

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

    //住所関連
    Route::get('/purchase/address/{item}', [PurchaseController::class, 'editAddress'])->name('purchase.editAddress');
    Route::post('/purchase/address/update', [PurchaseController::class, 'updateAddress'])->name('purchase.updateAddress');


    //ログアウト
    Route::post('/logout', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

