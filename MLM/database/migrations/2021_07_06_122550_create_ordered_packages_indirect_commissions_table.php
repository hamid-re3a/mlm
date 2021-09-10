<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedPackagesIndirectCommissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordered_packages_indirect_commissions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('ordered_package_id');

            $table->integer('level');
            $table->integer('percentage');

            $table->unique(['ordered_package_id', 'level'],'ordered_package_id_unique');

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
        Schema::dropIfExists('ordered_packages_indirect_commissions');
    }
}
