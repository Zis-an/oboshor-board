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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('account_no')->nullable();
            $table->unsignedBigInteger('bank_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('account_type_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->float('interest_rate')->nullable();
            $table->integer('maturity_period')->nullable();
            $table->timestamps();

            $table->foreign('bank_id')->references('id')
                ->on('banks')->cascadeOnDelete();

            $table->foreign('branch_id')->references('id')
                ->on('branches')->cascadeOnDelete();

            $table->foreign('account_type_id')->references('id')
                ->on('account_types')->cascadeOnDelete();

            $table->foreign('created_by')->references('id')
                ->on('users')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
};
