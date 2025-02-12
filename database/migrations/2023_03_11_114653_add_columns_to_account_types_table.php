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
        Schema::table('account_types', function (Blueprint $table) {
            $table->boolean('allow_withdraw')->after('name');
            $table->boolean('allow_deposit')->after('allow_withdraw');
            $table->boolean('has_interest')->after('allow_deposit');
            $table->boolean('has_maturity_period')->after('has_interest');
            $table->boolean('is_active')->default(true)->after('has_interest');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_types', function (Blueprint $table) {
            //
        });
    }
};
