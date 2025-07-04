<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCategoryFromGoodsTable extends Migration
{
    // 生成されたマイグレーションファイルに以下を記述：
    public function up()
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    public function down()
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->string('category'); // 必要に応じて型と制約を調整
        });
    }
}
