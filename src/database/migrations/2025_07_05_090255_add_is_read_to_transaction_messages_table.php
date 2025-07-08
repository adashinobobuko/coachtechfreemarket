<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsReadToTransactionMessagesTable extends Migration
{
    public function up()
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('message'); // 適宜位置は調整
        });
    }
    
    public function down()
    {
        Schema::table('transaction_messages', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
    
}
