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
        Schema::create('passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->references('id')->on('bookings')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_data_id')->references('id')->on('user_data')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('place_from', 5)->nullable();
            $table->string('place_back', 5)->nullable();
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
        Schema::dropIfExists('passengers');
    }
};
