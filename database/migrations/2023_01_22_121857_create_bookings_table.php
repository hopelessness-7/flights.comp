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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('flight_from')->references('id')->on('flights')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('flight_back')->references('id')->on('flights')->constrained()->cascadeOnDelete()->cascadeOnUpdate()->nullable();
            $table->date('date_from');
            $table->time('date_back')->nullable();
            $table->string('code', 5);
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
        Schema::dropIfExists('bookings');
    }
};
