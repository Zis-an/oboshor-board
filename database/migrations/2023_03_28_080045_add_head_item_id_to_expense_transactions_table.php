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
        Schema::table('expense_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('head_item_id');
            $table->unsignedBigInteger('quantity')->default(0);
            $table->string('unit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expense_transactions', function (Blueprint $table) {
            //
        });
    }
};
