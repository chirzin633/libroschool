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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_code')->unique();
            $table->string('name');
            $table->enum('type', ['Guru', 'Siswa']);
            $table->string('class_position')->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('phone');
            $table->string('address');
            $table->string('photo')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->date('registered_at');
            $table->date('inactive_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
