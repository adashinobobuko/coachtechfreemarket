<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('goods', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable(); // 商品画像のURL
            $table->string('category'); // カテゴリー
            $table->string('condition'); // 商品の状態
            $table->string('name'); // 商品名
            $table->text('description'); // 商品の説明
            $table->unsignedInteger('price'); // 販売価格
            $table->unsignedInteger('favorites_count')->default(0); // お気に入り数
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goods');
    }
};

