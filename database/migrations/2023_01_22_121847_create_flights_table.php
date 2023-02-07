<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flights', function (Blueprint $table) {
            $table->id();
            $table->string('flight_code', 10);
            $table->foreignId('from_id')->references('id')->on('airports')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('to_id')->references('id')->on('airports')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->time('time_from');
            $table->time('time_to');
            $table->integer('cost');
            $table->date('date');
            $table->string('count_passengers');
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
        Schema::dropIfExists('flights');
    }
};
