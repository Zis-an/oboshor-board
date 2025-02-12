<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expense_transactions', function (Blueprint $table) {
            $table->id();
            $table->text('description')->nullable();
            $table->float('amount', 10, 2);
            $table->unsignedBigInteger('head_id');
            $table->unsignedBigInteger('transaction_id');
            $table->timestamps();

            $table->foreign('head_id')->references('id')
                ->on('heads')->cascadeOnDelete();
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
        Schema::dropIfExists('expense_transactions');
    }
};
