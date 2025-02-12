<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnsToApprovalTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('approval_timelines', function (Blueprint $table) {
            $table->renameColumn('type_id', 'model_id');
            $table->renameColumn('table', 'model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('approval_timelines', function (Blueprint $table) {
            //
        });
    }
}
