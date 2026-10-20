@extends('layouts.app')

@php
    $baru = !$akun->exists;
@endphp

@section('title', ($baru ? 'Buat Akun' : 'Ubah Akun') . ' · ' . config('simpas.nama_aplikasi'))

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- ============ HEADER ============ --}}
    <div class="animate-fade-down">
        <a href="{{ route('akun.index') }}" class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-700 mb-3">
            <i data-lucide="arrow-left" class="size-4"></i>
            Kembali ke daftar akun
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            {{ $baru ? 'Buat Akun Baru' : 'Ubah Akun' }}
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            @if($baru)
                Password akan langsung aktif. Minta pengguna untuk menggantinya setelah masuk.
            @else
                Mengubah peran akun wali/santri harus diikuti perpindahan relasi ke data Santri yang benar.
            @endif
        </p>
    </div>

    {{-- ============ KESALAHAN FORMULIR ============ --}}
    @if($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 animate-scale-in" role="alert">
            <div class="flex items-start gap-2">
                <i data-lucide="circle-alert" class="size-4 mt-0.5 shrink-0"></i>
                <div>
                    <p class="font-semibold">Data belum tersimpan</p>
                    <ul class="mt-1 list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $pesan)
                            <li>{{ $pesan }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form method="POST"
          action="{{ $baru ? route('akun.store') : route('akun.update', $akun) }}"
          class="space-y-5">
        @csrf
        @unless($baru) @method('PUT') @endunless

        {{-- ============ DATA DASAR ============ --}}
        <div class="card p-5 sm:p-6 space-y-5 animate-fade-up">
            <h2 class="font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="user" class="size-4 text-brand-600"></i>
                Data Akun
            </h2>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="label">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input id="name" type="text" name="name" required maxlength="120"
                           value="{{ old('name', $akun->name) }}"
                           @class(['input ring-rose-300 bg-rose-50/40' => $errors->has('name')])
                           placeholder="mis. Achlis Chafiyyu">
                    @error('name')
                        <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                            <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="username" class="label">Kode Akun <span class="text-rose-500">*</span></label>
                    <input id="username" type="text" name="username" required maxlength="120"
                           value="{{ old('username', $akun->username) }}"
                           autocapitalize="none" spellcheck="false"
                           @class(['input tabular ring-rose-300 bg-rose-50/40' => $errors->has('username')])
                           placeholder="mis. wali031">
                    <p class="mt-1.5 text-[12px] text-slate-500">
                        Huruf, angka, titik, garis bawah, dan tanda hubung. Harus unik.
                    </p>
                    @error('username')
                        <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                            <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="label">Email <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input id="email" type="email" name="email" maxlength="190"
                           value="{{ old('email', $akun->email) }}"
                           @class(['input ring-rose-300 bg-rose-50/40' => $errors->has('email')])
                           placeholder="mis. wali@pondok.sch.id">
                    @error('email')
                        <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                            <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="telepon" class="label">Nomor Telepon <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input id="telepon" type="text" name="telepon" maxlength="40"
                           value="{{ old('telepon', $akun->telepon) }}"
                           class="input tabular" placeholder="mis. 0812xxxxxxx">
                    @error('telepon')
                        <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                            <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ============ PERAN & RELASI ============ --}}
        <div class="card p-5 sm:p-6 space-y-5 animate-fade-up">
            <h2 class="font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="shield" class="size-4 text-brand-600"></i>
                Peran &amp; Relasi Santri
            </h2>

            <div>
                <label for="role" class="label">Peran <span class="text-rose-500">*</span></label>
                <select id="role" name="role" required
                        @class(['input ring-rose-300 bg-rose-50/40' => $errors->has('role')])>
                    @foreach([
                        'wali' => 'Wali Santri — hanya melihat data anaknya',
                        'santri' => 'Santri — hanya melihat datanya sendiri',
                        'admin' => 'Admin/Pengurus — mengelola seluruh data',
                    ] as $v => $l)
                        <option value="{{ $v }}" @selected(old('role', $akun->role) === $v)>{{ $l }}</option>
                    @endforeach
                </select>
                @error('role')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                        <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Hanya wali/santri yang wajib tertaut ke satu Santri. --}}
            <div id="wrap-santri">
                <label for="santri_id" class="label">
                    Data Santri Tertaut
                    <span id="tanda-wajib-santri" class="text-rose-500">*</span>
                </label>
                <select id="santri_id" name="santri_id"
                        @class(['input ring-rose-300 bg-rose-50/40' => $errors->has('santri_id')])>
                    <option value="">— Pilih data Santri —</option>
                    @foreach($santris as $s)
                        <option value="{{ $s->id }}"
                            @selected((int) old('santri_id', $akun->santri_id) === (int) $s->id)>
                            {{ $s->nis }} — {{ $s->nama }}
                            @if($s->kamar) ({{ $s->kamar }}) @endif
                        </option>
                    @endforeach
                </select>
                <p class="mt-1.5 text-[12px] text-slate-500">
                    Akun wali hanya dapat membuka data Santri yang dipilih di sini. Mengetahui NIS saja tidak memberi akses.
                </p>
                @error('santri_id')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                        <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Konfirmasi khusus admin: mengetik ulang password sendiri. --}}
            <div id="wrap-konfirmasi" class="hidden rounded-2xl border border-amber-200 bg-amber-50 p-4">
                <label for="konfirmasi_admin" class="label !text-amber-900">
                    Ketik Ulang Password Admin/Pengurus
                    <span class="text-rose-500">*</span>
                </label>
                <input id="konfirmasi_admin" type="password" name="konfirmasi_admin"
                       autocomplete="current-password" class="input !bg-white"
                       placeholder="Password akun Anda sendiri">
                <p class="mt-1.5 text-[12px] text-amber-800">
                    Membuat akun Admin/Pengurus bersifat sensitif, jadi konfirmasi dengan password Anda sendiri diperlukan.
                </p>
                @error('konfirmasi_admin')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                        <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        {{-- ============ PASSWORD ============ --}}
        @if($baru)
            <div class="card p-5 sm:p-6 space-y-5 animate-fade-up">
                <h2 class="font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="key-round" class="size-4 text-brand-600"></i>
                    Password Awal
                </h2>

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="label">Password <span class="text-rose-500">*</span></label>
                        <input id="password" type="password" name="password" required minlength="8"
                               autocomplete="new-password"
                               @class(['input ring-rose-300 bg-rose-50/40' => $errors->has('password')])
                               placeholder="minimal 8 karakter">
                        @error('password')
                            <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                                <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="label">Konfirmasi Password <span class="text-rose-500">*</span></label>
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               required minlength="8" autocomplete="new-password" class="input"
                               placeholder="ulangi password">
                    </div>
                </div>

                <p class="text-[12px] text-slate-500 flex items-start gap-1.5">
                    <i data-lucide="info" class="size-3.5 mt-0.5 shrink-0"></i>
                    Password disimpan dalam bentuk hash (bcrypt) dan tidak pernah bisa dibaca kembali,
                    bahkan oleh pengelola. Serahkan lewat kanal yang aman, lalu minta pengguna menggantinya.
                </p>
            </div>
        @endif

        {{-- ============ STATUS (khusus ubah) ============ --}}
        @unless($baru)
            <div class="card p-5 sm:p-6 animate-fade-up">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1"
                           @checked(old('is_active', $akun->isActive()))
                           class="size-4 mt-0.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span>
                        <span class="block font-semibold text-slate-800">Akun Aktif</span>
                        <span class="block text-[13px] text-slate-500">
                            Akun nonaktif tidak dapat masuk. Jika sedang memiliki sesi di perangkat lain,
                            aksesnya langsung tertutup pada permintaan berikutnya.
                        </span>
                    </span>
                </label>
                @error('is_active')
                    <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                        <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i>{{ $message }}
                    </p>
                @enderror
            </div>
        @endunless

        {{-- ============ TOMBOL ============ --}}
        <div class="flex items-center justify-end gap-2 animate-fade-up">
            <a href="{{ route('akun.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">
                <i data-lucide="check" class="size-4"></i>
                <span>{{ $baru ? 'Buat Akun' : 'Simpan Perubahan' }}</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    /*
     * Field relasi Santri hanya wajib untuk peran wali dan santri. Untuk admin
     * field disembunyikan dan dikosongkan di server, karena admin tidak punya
     * satu anak.
     *
     * Ini hanya membantu tampilan. Sifat wajibnya ditegakkan di server.
     */
    (function () {
        var role = document.getElementById('role');
        var wrapSantri = document.getElementById('wrap-santri');
        var tandaWajib = document.getElementById('tanda-wajib-santri');
        var selectSantri = document.getElementById('santri_id');
        var wrapKonfirmasi = document.getElementById('wrap-konfirmasi');
        var inputKonfirmasi = document.getElementById('konfirmasi_admin');

        function terapkan() {
            var admin = role.value === 'admin';
            var waliAtauSantri = role.value === 'wali' || role.value === 'santri';

            wrapKonfirmasi.classList.toggle('hidden', !admin);
            inputKonfirmasi.required = admin;
            if (!admin) { inputKonfirmasi.value = ''; }

            tandaWajib.classList.toggle('hidden', !waliAtauSantri);
            selectSantri.required = waliAtauSantri;
            if (admin) { selectSantri.value = ''; }
        }

        role.addEventListener('change', terapkan);
        terapkan();
    })();
</script>
@endpush
@endsection
