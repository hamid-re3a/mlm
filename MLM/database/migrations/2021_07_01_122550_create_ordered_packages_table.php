<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordered_packages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();

            $table->timestamp('is_paid_at')->nullable();
            $table->timestamp('is_resolved_at')->nullable();
            $table->timestamp('is_commission_resolved_at')->nullable();
            $table->integer('plan')->nullable();


            $table->integer('validity_in_days')->nullable();
            $table->double('price')->nullable();

            $table->integer('direct_percentage')->nullable();
            $table->integer('binary_percentage')->nullable();

            $table->timestamp('expires_at')->nullable();

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
        Schema::dropIfExists('ordered_packages');
    }
}
