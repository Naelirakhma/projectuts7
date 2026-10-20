<?php

namespace App\Http\Controllers;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Pengelolaan akun pengguna. Hanya Admin/Pengurus yang boleh masuk.
 *
 * Keputusan desain: TIDAK ADA register publik.
 *
 * Alasannya, penerapan untuk sistem internal pondok:
 *
 * - Aplikasi ini menyimpan data pribadi Orchids. Akun dibuat lebih baik oleh
 *   pengurus yang bisa memastikan orangnya benar dan hubungannya dengan
 *   Santri benar.
 * - Dengan tidak ada form publik, peran "admin" tidak pernah bisa dipilih
 *   oleh orang yang mendaftar sendiri.
 *
 * Aturan yang ditegakkan file ini:
 * - kode akun harus unik (dicek di server, bukan hanya di antarmuka);
 * - akun wali dan santri tidak mungkin dibuat tanpa relasi ke satu data Santri;
 * - membuat akun dengan peran admin memerlukan pengetikan ulang password
 *   Admin/Pengurus yang sedang login, supaya tidak terjadi eskalasi
 *   tak sengaja;
 * - akun tidak dihapus dari basis data, hanya dinonaktifkan, sehingga riwayat
 *   aktivitas yang menunjuk user_id tidak jadi yatim.
 */
class AkunController extends Controller
{
    /** Menu utama. */
    public function index(Request $request)
    {
        $this->requireAdmin();

        // Relasi yang dimuat adalah 'santris' (banyak anak), bukan 'santri'.
        // Nama 'santri' tidak lagi menjadi relasi Eloquent, melainkan accessor
        // yang mengembalikan anak utama; with('santri') akan gagal karena
        // Eloquent hanya mengenali method relasi.
        $query = User::query()->with('santris');

        if ($request->filled('q')) {
            $cari = $request->string('q')->trim();
            $query->where(function ($sub) use ($cari) {
                $sub->where('username', 'like', "%{$cari}%")
                    ->orWhere('name', 'like', "%{$cari}%");
            });
        }

        if ($request->filled('peran')) {
            $peran = $request->string('peran')->toString();
            if (in_array($peran, [User::ROLE_ADMIN, User::ROLE_WALI, User::ROLE_SANTRI], true)) {
                $query->where('role', $peran);
            }
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->when(
                $status === 'aktif',
                fn ($q) => $q->where(function ($inner) {
                    $inner->where('is_active', true)->orWhereNull('is_active');
                })
            )->when(
                $status === 'nonaktif',
                fn ($q) => $q->where('is_active', false)
            );
        }

        $akun = $query->orderByRaw("FIELD(role, 'admin', 'wali', 'santri')")
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('akun.index', [
            'akun' => $akun,
            'statistik' => [
                'total' => User::count(),
                'admin' => User::where('role', User::ROLE_ADMIN)->count(),
                'wali' => User::where('role', User::ROLE_WALI)->count(),
                'santri' => User::where('role', User::ROLE_SANTRI)->count(),
                'nonaktif' => User::where('is_active', false)->count(),
            ],
        ]);
    }

    /** Formulir tambah akun. */
    public function create()
    {
        $this->requireAdmin();

        return view('akun.form', [
            'akun' => new User(['is_active' => true]),
            'santris' => $this->daftarSantri(),
        ]);
    }

    /** Menyimpan akun baru. */
    public function store(Request $request)
    {
        $this->requireAdmin();

        $data = $request->validate($this->rules($request), $this->pesan(), $this->namaKolom());

        // Email opsional, tapi kalau diisi harus unik dan valid.
        $email = $request->filled('email') ? strtolower(trim($request->input('email'))) : null;

        if ($email !== null) {
            $bentrok = User::withTrashed()->where('email', $email)->exists();
            if ($bentrok) {
                return back()->withInput()->withErrors([
                    'email' => 'Email tersebut sudah dipakai akun lain.',
                ]);
            }
        }

        // Kode akun unik termasuk akun yang dinonaktifkan, supaya kode lama
        // tidak pernah hidup kembali untuk orang yang berbeda.
        if (User::withTrashed()->where('username', $request->input('username'))->exists()) {
            return back()->withInput()->withErrors([
                'username' => 'Kode akun sudah dipakai. Gunakan kode lain.',
            ]);
        }

        $akun = new User();
        $akun->name = $request->string('name')->trim()->toString();
        $akun->username = $request->string('username')->trim()->toString();
        $akun->email = $email;
        $akun->role = $data['role'];
        $akun->telepon = $request->filled('telepon')
            ? $request->string('telepon')->trim()->toString()
            : null;
        $akun->is_active = (bool) ($data['is_active'] ?? true);

        // Cast 'hashed' pada model yang akan mem-bcrypt nilai ini.
        $akun->password = $data['password'];
        $akun->password_changed_at = now();

        // Hanya wali dan santri yang terikat ke data Santri. Admin tidak punya.
        $akun->santri_id = $akun->isAdmin() ? null : $data['santri_id'];

        $akun->save();

        /*
         * Akun wali/santri WAJIB punya baris di guardian_santri, bukan hanya
         * mengisi kolom users.santri_id. Penulisan hanya pada kolom itu membuat
         * $akun->santris() tetap kosong, lalu hasSantriLink() menolak akun
         * tersebut saat login dengan pesan "belum terhubung".
         */
        if ($akun->isAdmin()) {
            $akun->santris()->detach();
            $akun->forceFill(['santri_id' => null])->saveQuietly();
        } else {
            $akun->tautkanSantri(
                (int) $akun->santri_id,
                $request->string('hubungan')->trim()->toString() ?: 'Wali'
            );
        }

        Log::info('akun dibuat', [
            'user_id_baru' => $akun->id,
            'username' => $akun->username,
            'role' => $akun->role,
            'jumlah_anak' => $akun->santris()->count(),
            'oleh' => $request->user()->id,
        ]);

        return redirect()->route('akun.index')->with('toast', [
            'type' => 'success',
            'message' => "Akun {$akun->username} berhasil dibuat.",
        ]);
    }

    /** Formulir ubah akun. */
    public function edit(User $akun)
    {
        $this->requireAdmin();

        return view('akun.form', [
            'akun' => $akun,
            'santris' => $this->daftarSantri(),
        ]);
    }

    /** Menyimpan perubahan akun. */
    public function update(Request $request, User $akun)
    {
        $this->requireAdmin();

        $data = $request->validate($this->rules($request, $akun), $this->pesan(), $this->namaKolom());

        // Admin tidak boleh mengunci dirinya sendiri dari aplikasi.
        if ($request->user()->id === $akun->id) {
            if (! ($data['is_active'] ?? true)) {
                return back()->withInput()->withErrors([
                    'is_active' => 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.',
                ]);
            }
        }

        // Sistem harus selalu punya satu admin aktif, kalau tidak tidak ada yang
        // bisa masuk untuk memperbaiki keadaan.
        if ($akun->isAdmin() && ! ($data['is_active'] ?? true) && $this->jumlahAdminAktif() <= 1) {
            return back()->withInput()->withErrors([
                'is_active' => 'Minimal harus ada satu Admin/Pengurus yang aktif.',
            ]);
        }

        if ($akun->isAdmin() && $data['role'] !== User::ROLE_ADMIN) {
            return back()->withInput()->withErrors([
                'role' => 'Peran akun Admin/Pengurus tidak dapat diubah dari sini.',
            ]);
        }

        if ($request->filled('email')) {
            $email = strtolower(trim($request->input('email')));
            $bentrok = User::withTrashed()
                ->where('email', $email)
                ->where('id', '!=', $akun->id)
                ->exists();
            if ($bentrok) {
                return back()->withInput()->withErrors([
                    'email' => 'Email tersebut sudah dipakai akun lain.',
                ]);
            }
        }

        $akun->name = $request->string('name')->trim()->toString();
        $akun->username = $request->string('username')->trim()->toString();
        $akun->email = $request->filled('email') ? strtolower(trim($request->input('email'))) : null;
        $akun->telepon = $request->filled('telepon')
            ? $request->string('telepon')->trim()->toString()
            : null;
        $akun->role = $akun->isAdmin() ? User::ROLE_ADMIN : $data['role'];
        $akun->is_active = (bool) ($data['is_active'] ?? true);
        $akun->santri_id = $akun->isAdmin() ? null : $data['santri_id'];

        $akun->save();

        // Sama seperti saat pembuatan, relasi harus ditulis di guardian_santri.
        if ($akun->isAdmin()) {
            $akun->santris()->detach();
            $akun->forceFill(['santri_id' => null])->saveQuietly();
        } else {
            $akun->tautkanSantri(
                (int) $akun->santri_id,
                $request->string('hubungan')->trim()->toString() ?: 'Wali'
            );
        }

        Log::info('akun diperbarui', [
            'user_id' => $akun->id,
            'username' => $akun->username,
            'role' => $akun->role,
            'is_active' => $akun->is_active,
            'oleh' => $request->user()->id,
        ]);

        return redirect()->route('akun.index')->with('toast', [
            'type' => 'success',
            'message' => "Akun {$akun->username} berhasil diperbarui.",
        ]);
    }

    /** Mengatur ulang kata sandi akun lain. */
    public function resetPassword(Request $request, User $akun)
    {
        $this->requireAdmin();

        $data = $request->validate([
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ], [
            'password' => 'kata sandi',
        ]);

        $akun->password = $data['password'];
        $akun->password_changed_at = now();
        $akun->saveQuietly();

        // Sesi lama ikut gugur supaya Sandi baru benar-benar langsung berlaku.
        $akun->tokens()->where('name', 'remember_token')->delete();

        Log::info('password akun diatur ulang', [
            'user_id' => $akun->id,
            'oleh' => $request->user()->id,
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Kata sandi {$akun->username} berhasil diganti.",
        ]);
    }

    /** Menonaktifkan atau mengaktifkan kembali akun. */
    public function toggle(Request $request, User $akun)
    {
        $this->requireAdmin();

        if ($request->user()->id === $akun->id) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Anda tidak dapat menonaktifkan akun yang sedang digunakan.',
            ]);
        }

        if ($akun->isAdmin() && $akun->isActive() && $this->jumlahAdminAktif() <= 1) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Minimal harus ada satu Admin/Pengurus yang aktif.',
            ]);
        }

        $sekarang = ! $akun->isActive();
        $akun->is_active = $sekarang;
        $akun->save();

        Log::info('status akun diubah', [
            'user_id' => $akun->id,
            'is_active' => $sekarang,
            'oleh' => $request->user()->id,
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => $sekarang
                ? "Akun {$akun->username} diaktifkan kembali."
                : "Akun {$akun->username} dinonaktifkan.",
        ]);
    }

    /* ------------------------------------------------------------------ */
    /* Pembantu                                                           */
    /* ------------------------------------------------------------------ */

    /** Santri yang boleh dipilih sebagai anak dari akun wali. */
    private function daftarSantri()
    {
        return Santri::aktif()->orderBy('nis')->get(['id', 'nis', 'nama', 'kamar']);
    }

    private function jumlahAdminAktif(): int
    {
        return User::where('role', User::ROLE_ADMIN)
            ->where(function ($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->count();
    }

    /**
     * Aturan validasi.
     *
     * Sanity check: akun wali/santri WAJIB tertaut ke tepat satu data Santri.
     * Inilah yang mencegah orang mendapat akses hanya karena mengetahui NIS.
     */
    private function rules(Request $request, ?User $model = null): array
    {
        $idAkaun = $model?->id;

        $peran = $request->input('role', User::ROLE_WALI);

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'username' => [
                'required',
                'string',
                'max:120',
                // Huruf, angka, titik, garis bawah, dan tanda hubung saja.
                'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($idAkaun)->whereNull('deleted_at'),
            ],
            'email' => ['nullable', 'email', 'max:190'],
            'telepon' => ['nullable', 'string', 'max:40'],
            'hubungan' => ['nullable', 'string', 'max:40'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_WALI, User::ROLE_SANTRI])],
            'is_active' => ['nullable', 'boolean'],
            'santri_id' => [
                // Hanya wali/santri yang perlu tertaut ke data Santri.
                Rule::requiredIf(in_array($peran, [User::ROLE_WALI, User::ROLE_SANTRI], true)),
                'nullable',
                'integer',
                Rule::exists('santris', 'id')->whereNull('deleted_at'),
            ],
        ];

        // Password hanya diminta saat membuat, atau saat admin menghendaki reset.
        if ($model === null) {
            $rules['password'] = ['required', 'confirmed', Password::min(8)];
        }

        /*
         * Peran admin hanya boleh dipilih dengan mengetik ulang password
         * Admin/Pengurus yang sedang login. Tanpa ini, satu akun admin yang
         * berhasil login sudah bisa membuat akun admin lain tanpa jejak.
         */
        if ($peran === User::ROLE_ADMIN) {
            $rules['konfirmasi_admin'] = [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    $akunSaatIni = $request->user();

                    if (! $akunSaatIni || ! Hash::check($value, $akunSaatIni->password)) {
                        $fail('Password Admin/Pengurus tidak cocok.');
                    }
                },
            ];
        }

        return $rules;
    }

    private function pesan(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap terlalu panjang.',
            'username.required' => 'Kode akun wajib diisi.',
            'username.regex' => 'Kode akun hanya boleh berisi huruf, angka, titik, garis bawah, dan tanda hubung.',
            'username.unique' => 'Kode akun sudah dipakai.',
            'username.max' => 'Kode akun terlalu panjang.',
            'email.email' => 'Format email tidak valid.',
            'email.max' => 'Email terlalu panjang.',
            'telepon.max' => 'Nomor telepon terlalu panjang.',
            'role.required' => 'Peran wajib dipilih.',
            'role.in' => 'Peran yang dipilih tidak dikenal.',
            'santri_id.required' => 'Akun wali/santri wajib terhubung dengan satu data Santri.',
            'santri_id.exists' => 'Data Santri yang dipilih tidak ditemukan.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'konfirmasi_admin.required' => 'Ketik ulang password Admin/Pengurus untuk membuat akun admin.',
        ];
    }

    private function namaKolom(): array
    {
        return [
            'name' => 'nama lengkap',
            'username' => 'kode akun',
            'email' => 'email',
            'telepon' => 'nomor telepon',
            'role' => 'peran',
            'santri_id' => 'data Santri',
            'password' => 'kata sandi',
        ];
    }
}
