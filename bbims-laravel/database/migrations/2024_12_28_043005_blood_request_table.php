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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requesting_facility');
            $table->string('address');
            $table->string('pathologist');
            $table->string('facility_transac_num');
            $table->string('requested_by');
            $table->integer('uncrossmatched_whole_blood_a_plus')->default(0)->nullable();
            $table->integer('uncrossmatched_whole_blood_a_minus')->default(0)->nullable();
            $table->integer('uncrossmatched_whole_blood_b_plus')->default(0)->nullable();
            $table->integer('uncrossmatched_whole_blood_b_minus')->default(0)->nullable();
            $table->integer('uncrossmatched_whole_blood_o_plus')->default(0)->nullable();
            $table->integer('uncrossmatched_whole_blood_o_minus')->default(0)->nullable();
            $table->integer('uncrossmatched_whole_blood_ab_plus')->default(0)->nullable();
            $table->integer('uncrossmatched_whole_blood_ab_minus')->default(0)->nullable();
            $table->integer('uncrossmatched_packed_rbc_a_plus')->default(0)->nullable();
            $table->integer('uncrossmatched_packed_rbc_a_minus')->default(0)->nullable();
            $table->integer('uncrossmatched_packed_rbc_b_plus')->default(0)->nullable();
            $table->integer('uncrossmatched_packed_rbc_b_minus')->default(0)->nullable();
            $table->integer('uncrossmatched_packed_rbc_o_plus')->default(0)->nullable();
            $table->integer('uncrossmatched_packed_rbc_o_minus')->default(0)->nullable();
            $table->integer('uncrossmatched_packed_rbc_ab_plus')->default(0)->nullable();
            $table->integer('uncrossmatched_packed_rbc_ab_minus')->default(0)->nullable();
            $table->integer('crossmatched_whole_blood_a_plus')->default(0)->nullable();
            $table->integer('crossmatched_whole_blood_a_minus')->default(0)->nullable();
            $table->integer('crossmatched_whole_blood_b_plus')->default(0)->nullable();
            $table->integer('crossmatched_whole_blood_b_minus')->default(0)->nullable();
            $table->integer('crossmatched_whole_blood_o_plus')->default(0)->nullable();
            $table->integer('crossmatched_whole_blood_o_minus')->default(0)->nullable();
            $table->integer('crossmatched_whole_blood_ab_plus')->default(0)->nullable();
            $table->integer('crossmatched_whole_blood_ab_minus')->default(0)->nullable();
            $table->integer('crossmatched_packed_rbc_a_plus')->default(0)->nullable();
            $table->integer('crossmatched_packed_rbc_a_minus')->default(0)->nullable();
            $table->integer('crossmatched_packed_rbc_b_plus')->default(0)->nullable();
            $table->integer('crossmatched_packed_rbc_b_minus')->default(0)->nullable();
            $table->integer('crossmatched_packed_rbc_o_plus')->default(0)->nullable();
            $table->integer('crossmatched_packed_rbc_o_minus')->default(0)->nullable();
            $table->integer('crossmatched_packed_rbc_ab_plus')->default(0)->nullable();
            $table->integer('crossmatched_packed_rbc_ab_minus')->default(0)->nullable();
            $table->timestamps();
        });

        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blood_request_id');
            $table->string('blood_component');
            $table->string('blood_type');
            $table->integer('quantity');
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
        Schema::dropIfExists('requisition_items');
        Schema::dropIfExists('blood_requests');
    }
};