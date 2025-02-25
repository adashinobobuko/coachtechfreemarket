<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('purchases_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_id')->constrained()->onDelete('cascade'); // グッズID
            $table->string('postal_code')->nullable();   // 郵便番号
            $table->string('address')->nullable();       // 住所
            $table->string('building_name')->nullable(); // 建物名 
            $table->timestamps();           
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchases-addresses');
    }
}
