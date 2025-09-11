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
        Schema::create('cuts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['x', 'z'])->default('x');
            $table->date('date');
            $table->time('time');
            $table->integer('product_count')->nullable()->default(0);
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuts');
    }
};
