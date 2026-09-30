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
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_return_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['Late', 'Damaged', 'Lost']);
            $table->decimal('amount', 12, 2);
            $table->enum('status', ['Unpaid', 'Paid'])->default('Unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('book_return_id');
            // cegah duplikasi tipe denda per pengembalian
            $table->unique(['book_return_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
