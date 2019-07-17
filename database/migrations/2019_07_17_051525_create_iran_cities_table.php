<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIranCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('iran_cities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('type');
            $table->unsignedInteger('old_id')->nullable();
            $table->string('name');
            $table->boolean('center')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->integer('population')->nullable();
            $table->tinyInteger('favorite')->default(0);
            $table->nestedSet();
            $table->timestamps();
        });
        IranCitySeeder::run();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('iran_cities');
    }
}
