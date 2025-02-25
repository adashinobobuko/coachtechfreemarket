<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameGoodsIdToGoodIdInPurchasesAddresses extends Migration
{
    public function up()
    {
        Schema::table('purchases_addresses', function (Blueprint $table) {
            $table->renameColumn('goods_id', 'good_id');
        });
    }

    public function down()
    {
        Schema::table('purchases_addresses', function (Blueprint $table) {
            $table->renameColumn('good_id', 'goods_id');
        });
    }
}
