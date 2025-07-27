<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id')->nullable(false);
            $table->unsignedBigInteger('category_id')->nullable(false);
            $table->timestamps();


            // 外部キー制約
            $table->foreign('item_id')->references('id')->on('items');
            // itemsテーブルのidを参照
            $table->foreign('category_id')->references('id')->on('categories'); // categoriesテーブルのidを参照

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_categories');
    }
}
