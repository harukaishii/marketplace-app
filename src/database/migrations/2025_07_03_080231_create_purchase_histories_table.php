<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('item_id')->nullable(false);
            $table->unsignedBigInteger('user_address_id')->nullable(false);
            $table->tinyInteger('payment_type')->nullable(false);
            $table->timestamps();

            // 外部キー制約
            $table->foreign('item_id')->references('id')->on('items');
            // itemsテーブルのidを参照
            $table->foreign('user_id')->references('id')->on('users');
            // app_usersテーブルのidを参照
            $table->foreign('user_address_id')->references('id')->on('user_addresses');
            // usersテーブルのidを参照
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_histories');
    }
}
