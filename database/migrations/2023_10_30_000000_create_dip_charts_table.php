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
        Schema::create('dip_charts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tank_id');
            $table->decimal('depth', 10, 2);
            $table->decimal('volume', 10, 2);
            $table->unsignedBigInteger('entery_by_user')->nullable();
            $table->timestamps();

            $table->foreign('tank_id')->references('id')->on('tanks')->onDelete('cascade');
            $table->foreign('entery_by_user')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dip_charts');
    }
};
