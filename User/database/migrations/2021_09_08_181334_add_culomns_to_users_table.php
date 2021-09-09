<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCulomnsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('block_type')->after('email')->nullable();
            $table->boolean('is_freeze')->after('email')->default(FALSE)->nullable();
            $table->boolean('is_deactivate')->after('email')->default(FALSE)->nullable();
            $table->unsignedBigInteger('sponsor_id')->after('email')->nullable();
            $table->unsignedBigInteger('member_id')->after('email')->unsigned()->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
