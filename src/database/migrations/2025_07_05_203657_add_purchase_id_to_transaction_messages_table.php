<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurchaseIdToTransactionMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->foreignId('purchase_id')->constrained()->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->dropForeign(['purchase_id']);
            $table->dropColumn('purchase_id');
        });
    }
}
