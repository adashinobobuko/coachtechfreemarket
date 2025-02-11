<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->string('brand')->nullable()->after('category'); // ブランド名を追加
        });
    }

    public function down()
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn('brand');
        });
    }
};

