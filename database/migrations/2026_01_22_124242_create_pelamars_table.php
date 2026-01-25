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
        // GABUNGAN MIGRASI: Membuat tabel lengkap dari awal
        Schema::create('pelamars', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Data Utama
            $table->string('nama');
            $table->string('file_berkas')->nullable();
            $table->string('status_lamaran')->default('Pending');
            
            // Kolom Dinamis SPK (Pengganti tabel terpisah)
            $table->json('nilai_kriteria')->nullable(); 
            $table->double('skor_akhir')->default(0);

            // Kolom Legacy (Opsional, boleh dihapus jika 100% pakai JSON)
            $table->string('sertifikat')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('pengalaman')->nullable();
            $table->string('kesehatan')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelamars');
    }
};