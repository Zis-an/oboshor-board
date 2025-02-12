<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApprovalTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approval_timelines', function (Blueprint $table) {
            $table->id();
            //type
            $table->string('type');
            $table->string('table');
            $table->unsignedBigInteger('type_id');
            //default is 1
            $table->unsignedBigInteger('current_level')->default(1);
            $table->unsignedBigInteger('performed_by');
            $table->string('status')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('performed_by')->references('id')
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
        Schema::dropIfExists('approval_timelines');
    }
}
