<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResidualBonusSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('residual_bonus_settings', function (Blueprint $table) {
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
        Schema::dropIfExists('residual_bonus_settings');
    }
}
