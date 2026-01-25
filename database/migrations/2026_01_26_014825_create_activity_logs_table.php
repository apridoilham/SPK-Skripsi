<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            // Menyimpan ID User yang melakukan aksi (Admin/HRD)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Deskripsi aktivitas
            $table->text('description');
            // Warna label (opsional, untuk mempercantik UI)
            $table->string('type')->default('info'); // info, warning, danger
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};