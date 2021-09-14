<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageRoiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_rois', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->unsignedBigInteger('package_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('package_id')->on('packages')->references('id');

            $table->double('roi_percentage')->nullable();
            $table->date('due_date');

            $table->unique(['package_id','due_date']);
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
        Schema::dropIfExists('package_rois');
    }
}
