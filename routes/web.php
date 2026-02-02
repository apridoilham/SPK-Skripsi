<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SpkController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

// Redirect halaman awal ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Language Switcher
Route::get('lang/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'id'])) {
        return redirect()->back();
    }

    session(['locale' => $locale]);

    return redirect()->back()
        ->with('status', 'Language switched to ' . strtoupper($locale))
        ->cookie('locale', $locale, 60 * 24 * 30);
})->name('lang.switch');

// GROUP UTAMA: Hanya User yang SUDAH LOGIN
Route::middleware(['auth', 'verified'])->group(function () {
    
    // --- DASHBOARD & PROFILE (Semua Role Bisa Akses) ---
    // Controller dashboard sudah pintar memilah tampilan berdasarkan role user
    Route::get('/dashboard', [SpkController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Chatbot & AI Routes
    Route::post('/chat/send', [ChatbotController::class, 'sendMessage'])->name('chat.send');
    Route::post('/chat/extract-data', [ChatbotController::class, 'extractSupplierData'])->name('chat.extract'); // AI Data Extractor
    Route::post('/chat/explain-decision', [ChatbotController::class, 'explainDecision'])->name('chat.explain.decision'); // AI Decision Explainer
    Route::post('/chat/negotiation-coach', [ChatbotController::class, 'negotiationCoach'])->name('chat.negotiate'); // AI Negotiation Coach
    Route::post('/chat/teach', [ChatbotController::class, 'teachAi'])->name('chat.teach');
    Route::post('/chat/apply-criteria', [ChatbotController::class, 'applyCriteria'])->name('chat.apply');
    Route::post('/chat/analyze-cv', [ChatbotController::class, 'analyzeCv'])->name('chat.analyze'); // AI CV Analyzer


    // --- KHUSUS ROLE: STAFF (Legacy: pelamar) ---
    Route::middleware(['role:staff'])->group(function () {
        // Manage Suppliers
        Route::post('/supplier', [SpkController::class, 'storeSupplier'])->name('supplier.store');
        Route::put('/supplier/{id}', [SpkController::class, 'updateSupplier'])->name('supplier.update');
    });

    // --- KHUSUS ROLE: MANAGER (Legacy: hrd) ---
    Route::middleware(['role:hrd'])->group(function () {
        // Logika Perhitungan SPK (SAW)
        Route::get('/detail-perhitungan', [SpkController::class, 'detailPerhitungan'])->name('detail.perhitungan');
        Route::post('/hitung-ranking', [SpkController::class, 'prosesHitungRanking'])->name('ranking.hitung');
        Route::put('/nilai/{id}', [SpkController::class, 'updateNilai'])->name('nilai.update');
        Route::put('/status/{id}', [SpkController::class, 'updateStatus'])->name('status.update');
        Route::put('/kriteria/update', [SpkController::class, 'updateKriteria'])->name('kriteria.update');
        
        // Cetak & Download Laporan
        Route::get('/laporan/cetak', [SpkController::class, 'cetakLaporan'])->name('laporan.cetak');
        Route::get('/laporan/download', [SpkController::class, 'downloadLaporan'])->name('laporan.download');
    });

    // --- KHUSUS ROLE: ADMIN ---
    Route::middleware(['role:admin'])->group(function () {
        // Manajemen User (CRUD User)
        Route::post('/user', [SpkController::class, 'storeUser'])->name('user.store');
        Route::put('/user/{id}', [SpkController::class, 'updateUser'])->name('user.update');
        Route::delete('/user/{id}', [SpkController::class, 'deleteUser'])->name('user.destroy');
    });

    // --- AKSES FILE (Admin, Manager & Staff Boleh Lihat) ---
    // Route ini digunakan untuk membuka file PDF lamaran/supplier
    Route::middleware(['role:admin,hrd,staff'])->group(function() {
        Route::get('/view-pdf/{path}', [SpkController::class, 'viewPdf'])
            ->where('path', '.*') // Regex agar bisa membaca path folder
            ->name('view.pdf');
    });
});

require __DIR__.'/auth.php';
