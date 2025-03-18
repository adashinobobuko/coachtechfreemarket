<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('comment'); // comment カラムを削除
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->text('comment')->nullable(); // もしロールバックする場合、復元
        });
    }
};
