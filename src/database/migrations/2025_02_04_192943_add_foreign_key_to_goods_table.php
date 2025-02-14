<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
//TODO:このテーブルは削除してグッズテーブルにforeignidとして紐づけられないか？
return new class extends Migration {
    public function up()
    {
        Schema::table('goods', function (Blueprint $table) {
            if (!Schema::hasColumn('goods', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

    }

    public function down()
    {
        Schema::table('goods', function (Blueprint $table) {
            // 外部キーを削除
            $table->dropForeign(['user_id']);
        });
    }
};
