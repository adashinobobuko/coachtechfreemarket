<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->tinyInteger('rating'); // 1〜5
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->unique(['purchase_id', 'from_user_id']); // 二重評価を防ぐ
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
}
