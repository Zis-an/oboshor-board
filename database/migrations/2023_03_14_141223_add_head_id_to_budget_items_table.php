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
        Schema::table('budget_items', function (Blueprint $table) {
            $table->unsignedBigInteger('head_id')->nullable()
            ->after('budget_id');
            $table->unsignedBigInteger('head_item_id')->nullable();
            $table->unsignedBigInteger('parent_id')->nullable()->after('head_id');
            $table->foreign('head_id')->references('id')
                ->on('head_items')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('budgets', function (Blueprint $table) {
            //
        });
    }
};
