<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('cheque_number')->after('status')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('cheque_file')->nullable();
            $table->string('pay_order_number')->nullable();
            $table->date('pay_order_date')->nullable();
            $table->string('pay_order_file')->nullable();
            $table->string('beftn_transaction_id')->nullable();
            $table->string('bank')->nullable();
            $table->string('file')->nullable();
            $table->unsignedBigInteger('head_item_id')->nullable();

            $table->foreign('head_item_id')->references('id')
                ->on('head_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
}
