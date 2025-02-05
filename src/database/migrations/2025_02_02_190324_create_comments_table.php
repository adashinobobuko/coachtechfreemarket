<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('good_id')->constrained()->onDelete('cascade'); // 商品ID
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ユーザーID
            $table->text('comment'); // コメント内容
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
