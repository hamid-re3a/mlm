<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepthTreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trees', function (Blueprint $table) {
            $table->unsignedInteger('_dpt')->default(0);
            $table->tinyInteger('vacancy')->default(VACANCY_ALL);
            $table->index('vacancy');
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
        Schema::table('trees', function (Blueprint $table) {
            $table->dropColumn('_dpt');
            $table->dropIndex('_dpt');
            $table->dropColumn('vacancy');
            $table->dropIndex('vacancy');

        });
    }
}
