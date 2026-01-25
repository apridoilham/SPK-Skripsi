<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// Redirect halaman awal ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// GROUP UTAMA: Hanya User yang SUDAH LOGIN
Route::middleware(['auth', 'verified'])->group(function () {
    
    // --- DASHBOARD & PROFILE (Semua Role Bisa Akses) ---
    // Controller dashboard sudah pintar memilah tampilan berdasarkan role user
    Route::get('/dashboard', [SpkController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- KHUSUS ROLE: PELAMAR ---
    Route::middleware(['role:pelamar'])->group(function () {
        // Submit lamaran baru
        Route::post('/lamar', [SpkController::class, 'storeLamaran'])->name('lamar.store');
    });

    // --- KHUSUS ROLE: HRD ---
    Route::middleware(['role:hrd'])->group(function () {
        // Logika Perhitungan SPK (SAW)
        Route::post('/hitung-ranking', [SpkController::class, 'prosesHitungRanking'])->name('ranking.hitung');
        Route::put('/nilai/{id}', [SpkController::class, 'updateNilai'])->name('nilai.update');
        Route::put('/status/{id}', [SpkController::class, 'updateStatus'])->name('status.update');
        Route::put('/kriteria/update', [SpkController::class, 'updateKriteria'])->name('kriteria.update');
        
        // Cetak & Download Laporan
        Route::get('/laporan/cetak', [SpkController::class, 'cetakLaporan'])->name('laporan.cetak');
        Route::get('/laporan/download', [SpkController::class, 'downloadLaporan'])->name('laporan.download');

        // AI Chatbot (Eksklusif HRD)
        Route::post('/asisten-ai/send', [ChatbotController::class, 'sendMessage'])->name('chat.send');
        Route::post('/asisten-ai/apply', [ChatbotController::class, 'applyCriteria'])->name('chat.apply');
    });

    // --- KHUSUS ROLE: ADMIN ---
    Route::middleware(['role:admin'])->group(function () {
        // Manajemen User (CRUD User)
        Route::post('/user', [SpkController::class, 'storeUser'])->name('user.store');
        Route::put('/user/{id}', [SpkController::class, 'updateUser'])->name('user.update');
        Route::delete('/user/{id}', [SpkController::class, 'deleteUser'])->name('user.destroy');
    });

    // --- AKSES FILE (Admin, HRD & Pelamar Boleh Lihat) ---
    // Route ini digunakan untuk membuka file PDF lamaran
    Route::middleware(['role:admin,hrd,pelamar'])->group(function() {
        Route::get('/view-pdf/{path}', [SpkController::class, 'viewPdf'])
            ->where('path', '.*') // Regex agar bisa membaca path folder
            ->name('view.pdf');
    });
});

require __DIR__.'/auth.php';