<?php

/*
|--------------------------------------------------------------------------
| SIMPAS - Sistem Informasi Monitoring dan Penilaian Aktivitas Santri
|--------------------------------------------------------------------------
| Konfigurasi terpusat untuk jenis aktivitas, aturan poin, status,
| dan aturan otomatisasi pelanggaran.
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Poin per status kehadiran
    |--------------------------------------------------------------------------
    | Berlaku untuk seluruh aktivitas, termasuk Diniyah.
    | Poin Diniyah TIDAK masuk ranking (lihat activities.diniyah.weight).
    */
    'points' => [
        'Hadir'      => 2,
        'Terlambat'  => 1,
        'Izin'       => 0,
        'Sakit'      => 0,
        'Udzur'      => 0,
        'Libur'      => 0,
        'Alfa'       => -2,
    ],

    // Status yang dipakai pada aktivitas harian Jamaah (spesifikasi bagian 10).
    'jamaah_statuses' => ['Hadir', 'Izin', 'Alfa'],

    // Status lengkap untuk aktivitas lain (Pengajian, Deresan, Sorogan, Murojaah, Ratiban, Diniyah).
    'full_statuses' => ['Hadir', 'Izin', 'Sakit', 'Udzur', 'Terlambat', 'Alfa', 'Libur'],

    /*
    |--------------------------------------------------------------------------
    | Jenis aktivitas
    |--------------------------------------------------------------------------
    | key        : identifier route/model
    | table      : tabel database
    | sessions   : daftar sesi pada satu tanggal (jamaah & deresan multi-sesi)
    | statuses   : status yang boleh dipilih
    | cadence    : harian | mingguan (hanya menentukan label tampilan)
    | weight     : true = ikut perhitungan ranking & poin aktivitas
    | violation   : aturan otomatisasi pelanggaran (null = tanpa otomatisasi)
    */
    'activities' => [
        'jamaah' => [
            'label'     => 'Jamaah',
            'table'     => 'jamaahs',
            'model'     => \App\Models\Jamaah::class,
            'icon'      => 'building-2',
            'color'     => 'emerald',
            'sessions'  => ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'],
            'statuses'  => ['Hadir', 'Izin', 'Alfa'],
            'cadence'   => 'harian',
            'weight'    => true,
            'violation' => ['threshold' => 3, 'days' => 7, 'jenis' => 'Tidak mengikuti jamaah', 'label' => 'Alfa Jamaah'],
        ],
        'pengajian' => [
            'label'     => 'Pengajian',
            'table'     => 'pengajians',
            'model'     => \App\Models\Pengajian::class,
            'icon'      => 'book-open',
            'color'     => 'amber',
            'sessions'  => ['Pengajian'],
            'statuses'  => ['Hadir', 'Izin', 'Sakit', 'Udzur', 'Terlambat', 'Alfa'],
            'cadence'   => 'harian',
            'weight'    => true,
            'violation' => ['threshold' => 3, 'days' => 30, 'jenis' => 'Tidak mengikuti pengajian', 'label' => 'Alfa Pengajian'],
        ],
        'deresan' => [
            'label'     => 'Deresan',
            'table'     => 'deresans',
            'model'     => \App\Models\Deresan::class,
            'icon'      => 'library-big',
            'color'     => 'sky',
            'sessions'  => ["Ba'da Subuh", "Ba'da Ashar"],
            'statuses'  => ['Hadir', 'Izin', 'Sakit', 'Udzur', 'Terlambat', 'Alfa'],
            'cadence'   => 'harian',
            'weight'    => true,
            'violation' => ['threshold' => 3, 'days' => 30, 'jenis' => 'Alfa', 'label' => 'Alfa Deresan'],
        ],
        'sorogan' => [
            'label'     => 'Sorogan',
            'table'     => 'sorogans',
            'model'     => \App\Models\Sorogan::class,
            'icon'      => 'book-copy',
            'color'     => 'violet',
            'sessions'  => ['Sorogan'],
            'statuses'  => ['Hadir', 'Izin', 'Sakit', 'Udzur', 'Terlambat', 'Alfa'],
            'cadence'   => 'harian',
            'weight'    => true,
            'violation' => ['threshold' => 3, 'days' => 30, 'jenis' => 'Alfa', 'label' => 'Alfa Sorogan'],
        ],
        'murojaah' => [
            'label'     => 'Murojaah',
            'table'     => 'murojaahs',
            'model'     => \App\Models\Murojaah::class,
            'icon'      => 'bookmark-check',
            'color'     => 'teal',
            'sessions'  => ['Murojaah'],
            'statuses'  => ['Hadir', 'Izin', 'Sakit', 'Udzur', 'Terlambat', 'Alfa'],
            'cadence'   => 'mingguan',
            'weight'    => true,
            'violation' => ['threshold' => 3, 'days' => 30, 'jenis' => 'Tidak mengikuti kegiatan', 'label' => 'Alfa Murojaah'],
        ],
        'ratiban' => [
            'label'     => 'Ratiban',
            'table'     => 'ratibans',
            'model'     => \App\Models\Ratiban::class,
            'icon'      => 'hand-heart',
            'color'     => 'rose',
            'sessions'  => ['Ratiban'],
            'statuses'  => ['Hadir', 'Izin', 'Sakit', 'Udzur', 'Terlambat', 'Alfa'],
            'cadence'   => 'mingguan',
            'weight'    => true,
            'violation' => ['threshold' => 3, 'days' => 30, 'jenis' => 'Tidak mengikuti kegiatan', 'label' => 'Alfa Ratiban'],
        ],
        'diniyah' => [
            'label'     => 'Diniyah',
            'table'     => 'diniyahs',
            'model'     => \App\Models\Diniyah::class,
            'icon'      => 'graduation-cap',
            'color'     => 'indigo',
            'sessions'  => ['Diniyah'],
            'statuses'  => ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Udzur', 'Libur', 'Alfa'],
            'cadence'   => 'harian',
            'weight'    => false,   // Poin Diniyah tidak masuk ranking
            'violation' => null,     // Diniyah tanpa otomatisasi pelanggaran
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Aturan otomatisasi pelanggaran
    |--------------------------------------------------------------------------
    | Sistem mengecek absensi dan membuat pelanggaran otomatis satu kali
    | per santri + periode + jenis (dicegah lewat kolom auto_key yang unik).
    */
    'auto_violation' => [
        'enabled'   => true,
        'tingkat'   => 'Sedang',
        'poin'      => -5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Poin pelanggaran berdasarkan tingkat (untuk acuan admin)
    |--------------------------------------------------------------------------
    */
    'pelanggaran_tingkat' => [
        'Ringan' => -3,
        'Sedang' => -5,
        'Berat'  => -10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Master data
    |--------------------------------------------------------------------------
    */
    'master' => [
        'jenis_prestasi'   => ['Akademik', "Al-Qur'an", 'Keagamaan', 'Olahraga', 'Seni', 'Organisasi', 'Lainnya'],
        'tingkat_prestasi' => ['Pondok', 'Desa', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Nasional', 'Internasional'],
        'jenis_pelanggaran' => [
            'Tidak mengikuti jamaah',
            'Tidak mengikuti pengajian',
            'Alfa',
            'Terlambat',
            'Melanggar tata tertib',
            'Tidak mengikuti kegiatan',
            'Lainnya',
        ],
        'tingkat_pelanggaran' => ['Ringan', 'Sedang', 'Berat'],
        'status_aktif'        => ['Aktif', 'Alumni', 'Pindah', 'Keluar'],
        'pendidikan_formal'   => ['Pelajar', 'Mahasiswa', 'S1', 'Takhasus'],
        'jenis_diniyah'       => ['Diniyah', 'Asatidzah', 'Mutimin'],
        'level_diniyah'       => ["1 Wustho", "2 Wustho", "3 Wustho", "1 'Ulya", "2 'Ulya", "3 'Ulya"],
        'jenis_khatam'        => ['30 Juz', '20 Juz', '15 Juz', '10 Juz', '5 Juz', '1 Juz', 'Juz Amma'],
        'jenis_kelamin'       => ['Putra', 'Putri'],
        'kamar'               => ['Kamar 1', 'Kamar 2', 'Kamar 3', 'Kamar 4', 'Kamar 5'],
        'laporan'             => ['jamaah', 'pengajian', 'deresan', 'sorogan', 'murojaah', 'ratiban', 'diniyah', 'prestasi', 'pelanggaran'],
    ],

    'nama_aplikasi' => 'SIMPAS',
    'tagline'       => 'Sistem Informasi Monitoring dan Penilaian Aktivitas Santri - Komplek Roudlotul Qur\'an Putri PP Al Ihya Ulumaddin',

    /*
    |--------------------------------------------------------------------------
    | Pendaftaran mandiri & verifikasi OTP email
    |--------------------------------------------------------------------------
    |
    | Pendaftaran mandiri dipakai orang wali yang belum punya akun. Alurnya:
    | isi nama + email + nomor WhatsApp, kirim kode OTP ke email, masukkan kode.
    |
    | Batas pendaftaran:
    | - enabled = false menutup /daftar sepenuhnya tanpa menyentuh kode lain.
    | Pendaftaran lewat menu "Akun Pengguna" oleh Admin tetap jalan.
    | - Peran tidak pernah dipilih pemohon. Akun selalu dibuat sebagai wali.
    | - Jika nomor WhatsApp cocok dengan no_hp seorang Santri, akun langsung
    |   tertaut ke Santri itu. Bila tidak cocok, akun tetap aktif tetapi belum
    |   punya data anak; halaman wali akan menyuruh menghubungi pengurus.
    | - require_admin_approval = true menahan akun sampai Admin mengaktifkan.
    |
    */
    'registration' => [
        'enabled' => env('SIMPAS_REGISTRATION_ENABLED', true),

        // Peran tetap. Jangan diubah lewat .env: peran admin tidak boleh bisa
        // diminta oleh orang yang mendaftar sendiri.
        'default_role' => 'wali',

        // true = akun baru menunggu diaktifkan Admin/Pengurus.
        'require_admin_approval' => env('SIMPAS_REGISTRATION_NEEDS_APPROVAL', false),

        'otp' => [
            'length'    => 6,
            'ttl'       => 15,   // menit
            'max_attempts' => 5,  // tebakan per kode
            'resend_interval' => 2, // menit antar pengiriman ulang
            'max_resend' => 3,  // jumlah kirim ulang per hari
        ],
    ],
];
