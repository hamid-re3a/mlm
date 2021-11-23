<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepthReferralTreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('referral_trees', function (Blueprint $table) {
            $table->unsignedInteger('_dpt')->default(0);
            $table->index('_dpt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('referral_trees', function (Blueprint $table) {
            $table->dropColumn('_dpt');
            $table->dropIndex('_dpt');
        });
    }
}
