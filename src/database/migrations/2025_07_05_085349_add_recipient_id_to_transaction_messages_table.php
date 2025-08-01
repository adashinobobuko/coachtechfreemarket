<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecipientIdToTransactionMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('recipient_id')->after('user_id'); 
        });
    }
    
    public function down()
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->dropColumn('recipient_id');
        });
    }
}
