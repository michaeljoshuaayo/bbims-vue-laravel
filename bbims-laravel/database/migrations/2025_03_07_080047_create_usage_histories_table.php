<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usage_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blood_request_id');
            $table->string('blood_serial_number');
            $table->string('blood_type');
            $table->string('blood_component');
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('blood_request_id')->references('id')->on('blood_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usage_histories');
    }
};
