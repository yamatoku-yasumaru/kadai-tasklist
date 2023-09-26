<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // user_idカラムを追加
            $table->unsignedBigInteger('user_id');
            
            // 外部キー制約の削除
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            // 外部キー制約の削除
            $table->dropForeign('tasks_user_id_foreign');
           
            // user_idカラムを削除
            $table->dropColumn('user_id');
        });
    }
};
