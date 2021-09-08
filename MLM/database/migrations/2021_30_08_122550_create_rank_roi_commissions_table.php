<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankRoiCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rank_roi_commissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('rank');

            $table->integer('level');
            $table->integer('percentage');

            $table->unique(['rank', 'level']);

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
        Schema::dropIfExists('rank_roi_commissions');
    }
}
