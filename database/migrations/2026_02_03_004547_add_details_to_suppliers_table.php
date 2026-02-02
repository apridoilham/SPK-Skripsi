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
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('nama_barang')->nullable();
            $table->double('harga')->default(0);
            $table->string('tempo_pembayaran')->nullable();
            $table->string('estimasi_pengiriman')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['nama_barang', 'harga', 'tempo_pembayaran', 'estimasi_pengiriman']);
        });
    }
};
