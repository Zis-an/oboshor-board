<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('inventory_request_item_id')->nullable();
            $table->decimal('quantity');
            $table->decimal('issued_at');
            $table->timestamps();

            $table->foreign('user_id')->references('id')
                ->on('users')->cascadeOnDelete();

            $table->foreign('item_id')->references('id')
            ->on('items')->cascadeOnDelete();

            $table->foreign('inventory_request_item_id')->references('id')
                ->on('inventory_request_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
