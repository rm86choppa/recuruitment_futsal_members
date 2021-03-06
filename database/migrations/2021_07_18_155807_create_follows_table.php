<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFollowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('follows', function (Blueprint $table) {
            $table->Increments('id')->unsigned();

            //ユーザIDの外部キー設定
            //(参照してるユーザIDの登録が削除されたとき、そのIDを参照してるレコードも自動的に削除)
            $table->Integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            //投稿IDの外部キー設定
            //(参照してる投稿IDの登録が削除されたとき、そのIDを参照してるレコードも自動的に削除)
            $table->Integer('followed_user_id')->unsigned();
            $table->foreign('followed_user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('follows');
    }
}
