<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_WALI = 'wali';
    public const ROLE_SANTRI = 'santri';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'santri_id',
        'foto',
        'telepon',
        'is_active',
        'last_login_at',
        'password_changed_at',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Cast 'hashed' membuat Eloquent mem-bcrypt nilai yang di-set.
            // Karena itu controller boleh mengirim kata sandi polos dan tetap
            // aman, dan tidak ada jalur yang bisa menyimpan teks biasa.
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Relasi                                                             */
    /* ------------------------------------------------------------------ */

    /**
     * Anak-anak yang dipantau akun ini.
     *
     * Satu wali bisa punya banyak Santri, jadi relasinya banyak-ke-banyak lewat
     * tabel guardian_santri. Kolom users.santri_id tetap ada dan menandai anak
     * utama; kedua sumber itu harus selalu sinkron, lihat syncAnakUtama().
     */
    public function santris(): BelongsToMany
    {
        return $this->belongsToMany(Santri::class, 'guardian_santri')
            ->withPivot(['hubungan', 'is_primary'])
            ->withTimestamps()
            ->orderByDesc('guardian_santri.is_primary')
            ->orderBy('santris.nis');
    }

    /**
     * Anak utama.
     *
     * Dipakai oleh kode lama yang masih membaca $user->santri. Nilai ini selalu
     * milik salah satu anak yang sudah tertaut di guardian_santri.
     */
    public function anakUtama()
    {
        return $this->santri_id
            ? Santri::find($this->santri_id)
            : $this->santris()->orderByDesc('is_primary')->first();
    }

    /** Alias supaya $user->santri tetap bekerja seperti sebelumnya. */
    public function getSantriAttribute()
    {
        return $this->anakUtama();
    }

    public function waliDari()
    {
        return $this->belongsToMany(Santri::class, 'guardian_santri');
    }

    /* ------------------------------------------------------------------ */
    /* Sinkronisasi anak utama                                            */
    /* ------------------------------------------------------------------ */

    /**
     * Menautkan satu Santri ke akun ini.
     *
     * Bila ini anak pertama, anak utama otomatis ikut tertaut di kolom
     * users.santri_id supaya kode lama langsung ikut benar.
     */
    public function tautkanSantri(int $santriId, string $hubungan = 'Wali', bool $sebagaiUtama = false): void
    {
        $sudahAda = $this->santris()->where('santris.id', $santriId)->exists();

        if (! $sudahAda) {
            $menjadiAnakPertama = $this->santris()->count() === 0;

            $this->santris()->attach($santriId, [
                'hubungan' => $hubungan,
                'is_primary' => $sebagaiUtama || $menjadiAnakPertama,
            ]);
        } elseif ($sebagaiUtama) {
            $this->jadikanAnakUtama($santriId);
        }

        $this->refreshAnakUtama();
    }

    /** Melepas satu Santri dari akun ini. */
    public function lepaskanSantri(int $santriId): void
    {
        $iniAnakUtama = (int) $this->santri_id === $santriId;

        $this->santris()->detach($santriId);

        if ($iniAnakUtama) {
            $this->refreshAnakUtama();
        }
    }

    /** Mengganti anak utama tanpa mengubah daftar anak. */
    public function jadikanAnakUtama(int $santriId): void
    {
        if (! $this->santris()->where('santris.id', $santriId)->exists()) {
            return;
        }

        $this->santris()->updateExistingPivot($santriId, ['is_primary' => true]);

        // Hanya satu anak utama yang boleh aktif.
        $this->santris()
            ->where('santris.id', '!=', $santriId)
            ->updateExistingPivot(['is_primary' => false]);

        $this->forceFill(['santri_id' => $santriId])->saveQuietly();
    }

    /**
     * Menyamakan kolom users.santri_id dengan baris is_primary terbaru.
     *
     * Dipanggil setiap kali daftar anak berubah. Kalau tidak ada anak utama
     * (misalnya semua anak dilepas), kolom dikosongkan agar tidak menunjuk
     * Santri yang sudah tidak dipantau oleh akun ini lagi.
     */
    public function refreshAnakUtama(): void
    {
        $anakUtama = $this->santris()
            ->orderByDesc('is_primary')
            ->orderBy('santris.nis')
            ->first();

        if ($anakUtama) {
            $this->forceFill(['santri_id' => $anakUtama->id])->saveQuietly();
        } elseif ($this->santri_id !== null) {
            $this->forceFill(['santri_id' => null])->saveQuietly();
        }
    }

    /* ------------------------------------------------------------------ */
    /* Peran                                                              */
    /* ------------------------------------------------------------------ */

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isWali(): bool
    {
        return $this->role === self::ROLE_WALI;
    }

    public function isSantri(): bool
    {
        return $this->role === self::ROLE_SANTRI;
    }

    public function isWaliOrSantri(): bool
    {
        return in_array($this->role, [self::ROLE_WALI, self::ROLE_SANTRI], true);
    }

    /**
     * Halaman tujuan setelah login.
     *
     * Wali DAN Santri memakai halaman "/wali/*". Bedanya hanya isi: untuk wali
     * yang tampil adalah data anak, untuk Santri adalah data dirinya sendiri,
     * karena keduanya di-scope ke $user->santris().
     *
     * Keduanya harus diarahkan ke sini. Kalau hanya wali yang diarahkan,
     * Santri akan mendarat di dashboard admin dan melihat rekap seluruh pondok.
     */
    public function landingRoute(): string
    {
        return $this->isWaliOrSantri() ? 'wali.dashboard' : 'dashboard';
    }

    /* ------------------------------------------------------------------ */
    /* Status akun                                                        */
    /* ------------------------------------------------------------------ */

    /**
     * Akun hanya boleh login bila is_active bernilai benar.
     *
     * Kolomnya baru ada sejak migrasi 2026_10_14. Untuk akun lama yang dibuat
     * sebelum kolom itu ada, null diperlakukan sebagai aktif.
     */
    public function isActive(): bool
    {
        return $this->is_active === null ? true : (bool) $this->is_active;
    }

    public function scopeAktif(Builder $q): Builder
    {
        return $q->where(function (Builder $inner) {
            $inner->where('is_active', true)->orWhereNull('is_active');
        });
    }

    /**
     * Email sudah dibuktikan kepemilikannya lewat kode OTP.
     *
     * Akun yang dibuat Admin/Pengurus tidak punya email sama sekali, dan
     * kondisi itu dianggap sah karena peran serta relasinya sudah ditentukan
     * petugas, bukan oleh pemohon yang mendaftar sendiri.
     */
    public function emailTerverifikasi(): bool
    {
        return $this->email === null || $this->email_verified_at !== null;
    }

    /**
     * Akun wali/santri harus punya minimal satu Santri yang dipantau.
     * Admin tidak punya, jadi selalu lolos.
     */
    public function hasSantriLink(): bool
    {
        return $this->isAdmin() || $this->santris()->exists();
    }

    /* ------------------------------------------------------------------ */
    /* Label                                                              */
    /* ------------------------------------------------------------------ */

    public function getPeranLabelAttribute(): string
    {
        return [
            self::ROLE_ADMIN => 'Admin/Pengurus',
            self::ROLE_WALI => 'Wali Santri',
            self::ROLE_SANTRI => 'Santri',
        ][$this->role] ?? 'Tidak Diketahui';
    }

    public function getFotoUrlAttribute(): string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }

        return asset('images/logo-pondok.png');
    }
}
