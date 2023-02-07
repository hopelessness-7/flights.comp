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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('paymentId')->nullable();;
            $table->foreignId('booking_id')->references('id')->on('bookings')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('user_id')->references('id')->on('users')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('price')->default(0);
            $table->string('description')->nullable();
            $table->enum('status', ['CREATED', 'FAILED', 'CONFIRMED'])->default('CREATED');
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
        Schema::dropIfExists('transactions');
    }
};
