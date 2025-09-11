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
        Schema::create('cut_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cut_id')->constrained()->cascadeOnDelete();

            // Los valores que el usurio nos manda
            $table->float('cash', 10, 2)->nullable()->default(0.0);
            $table->float('card', 10, 2)->nullable()->default(0.0);
            // Los valores que sumamos apartir de las ventas del lote
            $table->float('cash_total', 10, 2)->nullable()->default(0.0);
            $table->float('card_total', 10, 2)->nullable()->default(0.0);
            $table->float('total', 10, 2)->nullable()->default(0.0);
            // Las diferencias que se generan
            $table->float('cash_difference', 10, 2)->nullable()->default(0.0);
            $table->float('card_difference', 10, 2)->nullable()->default(0.0);
            $table->float('total_difference', 10, 2)->nullable()->default(0.0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cut_details');
    }
};
