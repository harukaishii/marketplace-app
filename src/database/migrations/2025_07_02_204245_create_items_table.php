<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable(false);
            $table->string('brand_name', 255)->nullable();
            $table->integer('price')->nullable(false);
            $table->text('detail')->nullable(false);
            $table->Integer('condition')->nullable(false);
            $table->Integer('status')->nullable(false);
            $table->unsignedBigInteger('listed_by')->nullable(false);
            $table->string('image', 255)->nullable(false);
            $table->timestamps();

            // 外部キー制約
            $table->foreign('listed_by')->references('id')->on('users'); // app_usersテーブルのidを参照
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
