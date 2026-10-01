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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('value');
            $table->string('description')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'loan_days', 'value' => '7', 'description' => 'Lama peminjaman (hari)'],
            ['key' => 'max_books_per_borrowing', 'value' => '3', 'description' => 'Maksimal buku per transaksi'],
            ['key' => 'fine_late_per_day', 'value' => '1000', 'description' => 'Tarif denda keterlambatan per hari'],
            ['key' => 'fine_damaged_per_book', 'value' => '20000', 'description' => 'Tarif denda kerusakan per buku'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
