<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');

            $table->unsignedBigInteger('user_id');

            $table->string('name');
            $table->string('short_name',20);
            $table->integer('validity_in_days');
            $table->double('price');

            $table->integer('roi_percentage');
            $table->integer('direct_percentage');
            $table->integer('binary_percentage');


            $table->softDeletes();
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
        Schema::dropIfExists('packages');
    }
}
