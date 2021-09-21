<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Users extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->autoIncrement();

            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->string('username',100)->nullable();
            $table->string('email',100)->nullable();

            $table->integer('rank')->default(0);
            $table->enum('default_binary_position',[\MLM\Models\Tree::LEFT,\MLM\Models\Tree::RIGHT])->default(\MLM\Models\Tree::LEFT);

            $table->string('block_type')->nullable();
            $table->boolean('is_freeze')->default(FALSE)->nullable();
            $table->boolean('is_deactivate')->default(FALSE)->nullable();
            $table->unsignedBigInteger('sponsor_id')->nullable();
            $table->unsignedBigInteger('member_id')->unsigned()->nullable();

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
        Schema::dropIfExists('users');
    }
}
