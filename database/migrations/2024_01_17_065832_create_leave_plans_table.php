<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('balance', 8, 2);
            $table->decimal('taken', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leave_plans');
    }
}
