<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagePathToTransactionMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('message');
        });
    }
    
    public function down()
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
    
}
