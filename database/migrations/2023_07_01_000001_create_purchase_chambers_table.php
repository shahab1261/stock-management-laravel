<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseChambersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('purchase_chambers')) {
            Schema::create('purchase_chambers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('purchase_id');
                $table->integer('chamber_number');
                $table->decimal('capacity', 10, 2)->default(0);
                $table->decimal('dip', 10, 2)->default(0);
                $table->decimal('rec_dip', 10, 2)->default(0);
                $table->decimal('gain_loss', 10, 2)->default(0);
                $table->decimal('ltr', 10, 2)->default(0);
                $table->timestamps();

                $table->foreign('purchase_id')->references('id')->on('purchase')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_chambers');
    }
}
