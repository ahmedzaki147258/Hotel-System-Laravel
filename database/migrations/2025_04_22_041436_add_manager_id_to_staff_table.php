<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManagerIdToStaffTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->unsignedBigInteger('manager_id')->nullable()->after('id'); // Add the manager_id column
            $table->foreign('manager_id')->references('id')->on('staff')->onDelete('set null'); // Set foreign key reference
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('staff', function (Blueprint $table) {
            $table->dropForeign(['manager_id']); // Drop the foreign key
            $table->dropColumn('manager_id'); // Drop the manager_id column
        });
    }
}
