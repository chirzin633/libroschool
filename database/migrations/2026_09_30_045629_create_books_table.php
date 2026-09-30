<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('rack_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->year('publication_year')->nullable();
            $table->string('isbn')->nullable();
            $table->decimal('price', 12, 2);
            $table->integer('stock');
            $table->integer('available_stock');
            $table->softDeletes();
            $table->timestamps();

            // Index untuk performa pencarian & dashboard
            $table->index('title');
            $table->index('available_stock');

            DB::statement('ALTER TABLE books ADD CONSTRAINT chk_books_stock CHECK (available_stock >= 0 AND available_stock <= stock)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
