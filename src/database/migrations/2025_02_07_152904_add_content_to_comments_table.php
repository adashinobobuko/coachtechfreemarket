<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->text('content')->nullable(); // `content` カラムを追加
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->text('comment')->nullable();
            $table->dropColumn('content');
        });
    }
};


