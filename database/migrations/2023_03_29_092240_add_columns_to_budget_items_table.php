<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToBudgetItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('budget_items', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable();
            $table->decimal('quantity', 10);
            $table->string('unit');
            $table->foreign('item_id')->references('id')
                ->on('items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('budget_items', function (Blueprint $table) {
            //
        });
    }
}
