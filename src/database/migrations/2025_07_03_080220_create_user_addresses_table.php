<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false)->unique();
            $table->string('post', 255)->nullable(false);
            $table->string('address', 255)->nullable(false);
            $table->string('building', 255)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            // app_usersテーブルのidを参照
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_addresses');
    }
}
