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
        Schema::create('dips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entery_by_user')->nullable();
            $table->unsignedBigInteger('tankId');
            $table->unsignedBigInteger('productId');
            $table->decimal('dip_value', 10, 2);
            $table->decimal('liters', 10, 2);
            $table->date('dip_date');
            $table->decimal('previous_stock', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('entery_by_user')->references('id')->on('users')->onDelete('set null');
            $table->foreign('tankId')->references('id')->on('tanks')->onDelete('cascade');
            $table->foreign('productId')->references('id')->on('products')->onDelete('cascade');

            // Ensure unique dip per tank per date
            $table->unique(['tankId', 'dip_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dips');
    }
};
