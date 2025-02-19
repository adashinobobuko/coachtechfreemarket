<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSoldToGoodsTable extends Migration
{

    public function up()
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->boolean('is_sold')->default(false)->after('price');//デフォルトはfalse（未完売）
        });
    }

    
    public function down()
    {
        Schema::table('goods', function (Blueprint $table) {
            $table->dropColumn('is_sold');
        });
    }
}
