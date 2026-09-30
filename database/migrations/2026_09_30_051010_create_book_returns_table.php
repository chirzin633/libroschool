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
        Schema::create('book_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_detail_id')->unique()->constrained()->cascadeOnDelete();
            $table->date('returned_at');
            $table->enum('condition', ['Good', 'Damaged', 'Lost']);
            $table->integer('late_days')->default(0);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_returns');
    }
};
