<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('item_id')->nullable(false);
            $table->text('comment')->nullable(false);
            $table->timestamps();

            // 外部キー制約
            $table->foreign('item_id')->references('id')->on('items');
            // itemsテーブルのidを参照
            $table->foreign('user_id')->references('id')->on('users'); // app_usersテーブルのidを参照

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
