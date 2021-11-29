<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBecauseOfCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('commissions', function (Blueprint $table) {

            $table->unsignedBigInteger('because_of_ordered_package_id')->nullable();
            $table->foreign('because_of_ordered_package_id','commissions_because_of_ordered_package_id_fk')->references('id')->on('ordered_packages');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('commissions', function (Blueprint $table) {

            $table->dropColumn('because_of_ordered_package_id');
            $table->dropForeign('commissions_because_of_ordered_package_id_fk');

        });
    }
}
