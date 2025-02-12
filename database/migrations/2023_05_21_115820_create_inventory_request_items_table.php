<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryRequestItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_request_id');
            $table->decimal('quantity', 10);
            $table->string('priority');
            $table->timestamps();

            $table->foreign('inventory_request_id')->references('id')
                ->on('inventory_requests')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_request_items');
    }
}
