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
        Schema::rename('pelamars', 'suppliers');

        Schema::table('suppliers', function (Blueprint $table) {
            // Rename columns to fit Supplier context
            // 'nama' is fine (Nama Supplier)
            // 'file_berkas' is fine (File Penawaran PDF/Image)
            // 'status_lamaran' -> 'status_verifikasi' or just keep 'status'
            
            // Add specific supplier fields (can be extracted by AI)
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->text('catatan_negosiasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['email', 'telepon', 'catatan_negosiasi']);
        });

        Schema::rename('suppliers', 'pelamars');
    }
};
