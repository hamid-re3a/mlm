<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMLMSettingHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mlm_setting_histories', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->id();
            $table->foreignId('setting_id')->constrained('mlm_settings');
            $table->foreignId('actor_id')->constrained('users');
            $table->string('name');
            $table->string('value')->nullable();
            $table->string('title')->nullable();
            $table->mediumText('description')->nullable();
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
        Schema::dropIfExists('mlm_setting_histories');
    }
}
