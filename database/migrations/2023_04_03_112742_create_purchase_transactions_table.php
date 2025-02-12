<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_transactions', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->nullable();
            $table->float('amount', 10, 2);
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('transaction_id');
            $table->timestamps();

            $table->foreign('item_id')->references('id')
                ->on('items')->cascadeOnDelete();
            $table->foreign('transaction_id')->references('id')
                ->on('transactions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_transactions');
    }
}
