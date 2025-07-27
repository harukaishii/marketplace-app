<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseController;
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

Route::get('/', [ItemController::class,'index'])->name('index');
Route::get('/item/{item_id}',[ItemController::class,'show'])->name('item.show');

Route::middleware(['auth'])->group(function(){

    //コメント
    Route::post('/item/{itemId}/comment', [ItemController::class, 'storeComment'])->name('item.comment.store');

    //プロフィール関連
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/mypage', [ProfileController::class, 'show'])->name('profile');

    //出品
    Route::get('/sell', [ItemController::class, 'create'])->name('sell.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('sell.store');

    //購入
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('item.purchase');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'address'])->name('address.change');

    //ログアウト
    Route::post('/logout', [\Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

