<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_items', function (Blueprint $table) {
            $table->id();
            $table->string('serial_no')->nullable();
            $table->string('applicant_serial_no')->nullable();
            $table->date('date');
            $table->string('receiver_name');
            $table->string('index');
            $table->string('city');
            $table->float('amount', 12, 2);
            $table->string('bank_name');
            $table->string('branch_name');
            $table->string('account_no');
            $table->string('routing');
            $table->string('status')->nullable();
            $table->unsignedBigInteger('lot_id');
            $table->timestamps();

            $table->foreign('lot_id')->references('id')
                ->on('lots')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lot_items');
    }
};
