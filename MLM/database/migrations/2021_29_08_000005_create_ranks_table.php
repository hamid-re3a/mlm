<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranks', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->integer('rank')->unique();
            $table->string('rank_name');
            $table->unsignedBigInteger('condition_converted_in_bp');
            $table->unsignedBigInteger('condition_sub_rank');
            $table->boolean('condition_direct_or_indirect')->default(false);
            $table->unsignedBigInteger('prize_in_pf')->nullable();
            $table->string('prize_alternative')->nullable();
            $table->unsignedBigInteger('cap');
            $table->unsignedBigInteger('withdrawal_limit');
            $table->unsignedBigInteger('condition_number_of_left_children');
            $table->unsignedBigInteger('condition_number_of_right_children');
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
        Schema::dropIfExists('ranks');
    }
}
