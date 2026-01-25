<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // GABUNGAN MIGRASI
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();
            $table->string('kode');  // C1, C2, dst
            $table->string('nama');  // Kesehatan, Pengalaman, dst
            $table->float('bobot', 8, 3); // 0.623
            $table->enum('jenis', ['benefit', 'cost'])->default('benefit'); 
            $table->json('opsi')->nullable(); // Opsi Pilihan 1-5
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};