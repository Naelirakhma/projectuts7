<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AkunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PelanggaranController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\SampahController;
use App\Http\Controllers\WaliController;
use Illuminate\Support\Facades\Route;

/* ------------------------------------------------------------------ */
/* Autentikasi                                                         */
/* ------------------------------------------------------------------ */

/*
 * Pendaftaran mandiri memakai email + kode OTP. Peran tidak pernah dipilih
 * pemohon: akun pendaftaran selalu wali, apa pun isian form.
 */
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

    // throttle per alamat dibuat longgar (30/menit) dengan sengaja. Semua
    // pengguna di jaringan pondok sering berada di satu IP yang sama, jadi
    // batas kecil akan mengunci seluruh sekolah hanya karena beberapa orang
    // salah ketik. Perlindungan brute force yang sesungguhnya ada di
    // AuthController: 5 percobaan per kombinasi kode-akun + IP.
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:30,1')
        ->name('login.attempt');

    Route::get('/daftar', [PendaftaranController::class, 'form'])->name('daftar');
    // Batas per alamat dibuat longgar (10/menit). Satu keluarga bisa mendaftarkan
    // beberapa akun dari satu IP, dan seluruh pengguna pondok sering satu IP.
    // Pembatasan sesungguhnya ada di PendaftaranService: 3 permintaan kode per
    // email per jam.
    Route::post('/daftar', [PendaftaranController::class, 'kirimKode'])
        ->middleware('throttle:10,1');
    Route::get('/verifikasi', [PendaftaranController::class, 'formVerifikasi'])->name('daftar.verifikasi');
    Route::post('/verifikasi', [PendaftaranController::class, 'verifikasi'])->middleware('throttle:10,1');
    Route::post('/verifikasi/kirim-ulang', [PendaftaranController::class, 'kirimUlang'])
        ->middleware('throttle:3,1')
        ->name('daftar.verifikasi.kirim-ulang');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::redirect('/', '/dashboard');

/* ------------------------------------------------------------------ */
/* Halaman utama                                                       */
/* ------------------------------------------------------------------ */
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /* Data Santri (read-only untuk Santri/Wali) */
    Route::get('/santri', [SantriController::class, 'index'])->name('santri.index');
    Route::get('/santri/create', [SantriController::class, 'create'])->name('santri.create')->middleware('admin');
    Route::post('/santri', [SantriController::class, 'store'])->name('santri.store')->middleware('admin');
    Route::get('/santri/{santri}', [SantriController::class, 'show'])->name('santri.show');
    Route::get('/santri/{santri}/edit', [SantriController::class, 'edit'])->name('santri.edit')->middleware('admin');
    Route::put('/santri/{santri}', [SantriController::class, 'update'])->name('santri.update')->middleware('admin');
    Route::delete('/santri/{santri}', [SantriController::class, 'destroy'])->name('santri.destroy')->middleware('admin');

    /* Riwayat khataman (bagian 7) */
    Route::post('/santri/{santri}/khataman', [SantriController::class, 'simpanKhataman'])->name('santri.khataman.store')->middleware('admin');
    Route::delete('/khataman/{khataman}', [SantriController::class, 'hapusKhataman'])->name('khataman.destroy')->middleware('admin');

    /* API isi otomatis (bagian 8) */
    Route::get('/api/santri/cari', [SantriController::class, 'apiCari'])->name('api.santri.cari');
    Route::get('/api/santri/{santri}/profil', [SantriController::class, 'apiProfil'])->name('api.santri.profil');

    /* Aktivitas (bagian 9 - 16) */
    Route::get('/aktivitas', [ActivityController::class, 'menu'])->name('aktivitas.menu');
    Route::get('/aktivitas/{type}/riwayat', [ActivityController::class, 'riwayat'])->name('aktivitas.riwayat');
    Route::get('/aktivitas/{type}/detail/{santri}', [ActivityController::class, 'detail'])->name('aktivitas.detail');
    Route::get('/aktivitas/{type}/edit/{santri}', [ActivityController::class, 'edit'])->name('aktivitas.edit')->middleware('admin');
    Route::post('/aktivitas/{type}/update', [ActivityController::class, 'update'])->name('aktivitas.update')->middleware('admin');
    Route::delete('/aktivitas/{type}/hapus/{santri}', [ActivityController::class, 'destroy'])->name('aktivitas.destroy')->middleware('admin');
    Route::get('/aktivitas/{type}', [ActivityController::class, 'index'])->name('aktivitas.input')->middleware('admin');
    Route::post('/aktivitas/{type}', [ActivityController::class, 'store'])->name('aktivitas.store')->middleware('admin');

    /* Prestasi & Pelanggaran */
    Route::resource('prestasi', PrestasiController::class);
    Route::resource('pelanggaran', PelanggaranController::class);

    /* Ranking (bagian 23 - 25) */
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

    /* Laporan (bagian 26 - 35) */
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/{jenis}', [LaporanController::class, 'show'])->name('laporan.show');
    Route::get('/laporan/{jenis}/print', [LaporanController::class, 'print'])->name('laporan.print');
    Route::get('/laporan/{jenis}/excel', [LaporanController::class, 'excel'])->name('laporan.excel');

    /* Sampah (bagian 36) */
    Route::get('/sampah', [SampahController::class, 'index'])->name('sampah.index')->middleware('admin');
    Route::post('/sampah/{type}/{id}/restore', [SampahController::class, 'restore'])->name('sampah.restore')->middleware('admin');
    Route::delete('/sampah/{type}/hapus-semua', [SampahController::class, 'hapusSemua'])->name('sampah.hapus-semua')->middleware('admin');
    Route::delete('/sampah/{type}/{id}', [SampahController::class, 'permanen'])->name('sampah.permanen')->middleware('admin');
    Route::post('/sampah/{type}/{id}/restore', [SampahController::class, 'restore'])->name('sampah.restore')->middleware('admin');

    /* Pengaturan & Profile */
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index')->middleware('admin');
    Route::post('/pengaturan', [PengaturanController::class, 'simpan'])->name('pengaturan.simpan')->middleware('admin');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
});

/* ------------------------------------------------------------------ */
/* Kelola Akun (khusus Admin/Pengurus)                                 */
/* ------------------------------------------------------------------ */

/*
 * Catatan: middleware 'admin' di bawah adalah EnsureAdmin, bukan sekadar
 * pemeriksaan peran di dalam controller. Dipakai supaya halaman /akun tidak
 * bisa dibuka wali atau santri walau ada yang salah mengeklik tautan.
 */
Route::middleware(['auth', 'admin'])->prefix('akun')->name('akun.')->group(function () {
    Route::get('/', [AkunController::class, 'index'])->name('index');
    Route::get('/create', [AkunController::class, 'create'])->name('create');
    Route::post('/', [AkunController::class, 'store'])->name('store');
    Route::get('/{akun}/edit', [AkunController::class, 'edit'])->name('edit');
    Route::put('/{akun}', [AkunController::class, 'update'])->name('update');
    Route::post('/{akun}/password', [AkunController::class, 'resetPassword'])->name('password');
    Route::post('/{akun}/toggle', [AkunController::class, 'toggle'])->name('toggle');
});

/* ------------------------------------------------------------------ */
/* Wali Santri                                                         */
/* ------------------------------------------------------------------ */
Route::middleware(['auth', 'wali'])->prefix('wali')->name('wali.')->group(function () {
    Route::get('/dashboard', [WaliController::class, 'dashboard'])->name('dashboard');
    Route::get('/santri', [WaliController::class, 'santri'])->name('santri');
    Route::get('/aktivitas', [WaliController::class, 'aktivitas'])->name('aktivitas');
    Route::get('/prestasi', [WaliController::class, 'prestasi'])->name('prestasi');
    Route::get('/pelanggaran', [WaliController::class, 'pelanggaran'])->name('pelanggaran');
    Route::get('/khataman', [WaliController::class, 'khataman'])->name('khataman');
    Route::get('/ranking', [WaliController::class, 'ranking'])->name('ranking');
    Route::get('/laporan', [WaliController::class, 'laporan'])->name('laporan');
    Route::get('/profil', [WaliController::class, 'profil'])->name('profil');
    Route::post('/profil', [WaliController::class, 'updateProfil'])->name('profil.update');
    Route::post('/profil/password', [WaliController::class, 'password'])->name('profil.password');

    /*
     * Memilih anak yang ditampilkan. POST karena mengubah keadaan, dan
     * controller memvalidasi bahwa anak itu benar-benar milik akun wali.
     */
    Route::post('/pilih-santri', [WaliController::class, 'pilihSantri'])->name('pilih-santri');
});
