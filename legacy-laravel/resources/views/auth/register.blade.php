<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#059669">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Daftar Akun · {{ config('simpas.nama_aplikasi') }}</title>

    <link rel="icon" href="/images/logo-rq.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-slate min-h-screen relative">

<div class="fixed inset-0 z-0" aria-hidden="true">
    <img src="/images/bg-login.jpg" alt="" class="w-full h-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-br from-emerald-900/70 via-emerald-800/60 to-teal-900/70"></div>
</div>

<div class="min-h-screen grid lg:grid-cols-2 relative z-10">

    {{-- ============ SISI KIRI ============ --}}
    <div class="relative hidden lg:flex flex-col justify-between overflow-hidden p-12 text-white">
        <div class="blob size-72 bg-emerald-300/30 -top-16 -left-10"></div>
        <div class="blob size-96 bg-brand-400/30 bottom-10 -right-24" style="animation-delay:-3s"></div>

        <div class="relative flex items-center gap-3 animate-fade-down">
            <img src="/images/logo-pondok.png" alt="Logo Pondok" class="size-12 rounded-2xl drop-shadow-xl">
            <img src="/images/logo-rq.png" alt="Logo Roudlotul Qur'an" class="size-12 rounded-full drop-shadow-xl bg-white/10 p-1">
            <div>
                <p class="text-2xl font-extrabold tracking-tight text-white">SIMPAS</p>
                <p class="text-xs text-emerald-100/90 mt-1">{{ config('simpas.tagline') }}</p>
            </div>
        </div>

        <div class="relative animate-fade-up" style="animation-delay:.15s">
            <p class="text-sm font-semibold text-emerald-200 tracking-[0.2em] uppercase mb-4">Wali Santri</p>
            <h2 class="text-4xl xl:text-5xl font-extrabold leading-tight tracking-tight">
                Pantau_course perkembangan<br>
                <span class="text-amber-300">anak Anda</span>,.
            </h2>

            <ol class="mt-10 space-y-4 max-w-md">
                @foreach([
                    ['n' => '1', 'j' => 'Isi data diri', 'd' => 'Nama, email, dan nomor WhatsApp Anda.'],
                    ['n' => '2', 'j' => 'Masukkan kode OTP', 'd' => 'Kode dikirim ke email yang Anda isi.'],
                    ['n' => '3', 'j' => 'Masuk dan pantau', 'd' => 'DataSantri Anda langsung terhubung.'],
                ] as $langkah)
                    <li class="flex gap-4 items-start">
                        <span class="grid place-items-center size-9 rounded-xl bg-white/15 border border-white/20 font-bold shrink-0">
                            {{ $langkah['n'] }}
                        </span>
                        <span>
                            <span class="block font-semibold text-white">{{ $langkah['j'] }}</span>
                            <span class="block text-sm text-emerald-100/80">{{ $langkah['d'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ol>
        </div>

        <div class="relative animate-fade-in" style="animation-delay:.5s">
            <p class="text-[10px] text-emerald-200/50 text-right">&copy; {{ now()->year }} {{ config('simpas.nama_aplikasi') }}</p>
        </div>
    </div>

    {{-- ============ SISI KANAN (FORM) ============ --}}
    <div class="flex items-center justify-center p-6 sm:p-10 relative overflow-hidden">
        <div class="w-full max-w-md relative">

            <div class="lg:hidden flex items-center justify-center gap-3 mb-8 animate-fade-down">
                <img src="/images/logo-pondok.png" alt="Logo Pondok" class="size-10 rounded-2xl drop-shadow-xl">
                <p class="font-extrabold text-white text-xl leading-none">SIMPAS</p>
            </div>

            <div class="relative rounded-3xl bg-white/95 backdrop-blur-xl border border-white/30
                        shadow-2xl shadow-slate-900/10 p-7 sm:p-9 animate-fade-up">
                <div class="relative mb-7">
                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 border border-emerald-100
                                 px-3 py-1.5 text-[11px] font-bold text-emerald-700 uppercase tracking-wider mb-4">
                        <i data-lucide="user-plus" class="size-3"></i>
                        Pendaftaran Wali Santri
                    </span>
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Buat akun Anda</h1>
                    <p class="text-sm text-slate-500 mt-2">
                        Akun ini hanya untuk memantau data Santri. Akun Admin/Pengurus dibuat oleh pengurus pondok.
                    </p>
                </div>

                @if($errors->any())
                    <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 animate-scale-in" role="alert">
                        <div class="flex items-start gap-2">
                            <i data-lucide="circle-alert" class="size-4 mt-0.5 shrink-0"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('daftar') }}" class="space-y-4" id="form-daftar">
                    @csrf

                    <div>
                        <label for="name" class="label">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 size-[18px] text-slate-400 pointer-events-none"></i>
                            <input id="name" type="text" name="name" required maxlength="120"
                                   value="{{ old('name') }}" autocomplete="name"
                                   class="input !pl-12" placeholder="sesuai data wali">
                        </div>
                        @error('name')
                            <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                                <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i><span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="label">Email <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 size-[18px] text-slate-400 pointer-events-none"></i>
                            <input id="email" type="email" name="email" required maxlength="190"
                                   value="{{ old('email') }}" autocomplete="email" autocapitalize="none" spellcheck="false"
                                   class="input !pl-12" placeholder="nama@email.com">
                        </div>
                        <p class="mt-1.5 text-[12px] text-slate-500">Kode OTP dikirim ke alamat ini.</p>
                        @error('email')
                            <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                                <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i><span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="telepon" class="label">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <i data-lucide="phone" class="absolute left-4 top-1/2 -translate-y-1/2 size-[18px] text-slate-400 pointer-events-none"></i>
                            <input id="telepon" type="tel" name="telepon" required maxlength="20"
                                   value="{{ old('telepon') }}" autocomplete="tel" inputmode="tel"
                                   class="input !pl-12 tabular" placeholder="08xxxxxxxxxx">
                        </div>
                        <p class="mt-1.5 text-[12px] text-slate-500">
                            Dipakai untuk mencocokkan data Santri Anda. Nomor ini tidak dipakai untuk login.
                        </p>
                        @error('telepon')
                            <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                                <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i><span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="label">Password <span class="text-rose-500">*</span></label>
                            <input id="password" type="password" name="password" required minlength="8"
                                   autocomplete="new-password" class="input" placeholder="minimal 8 karakter">
                            @error('password')
                                <p class="mt-1.5 flex items-center gap-1.5 text-[13px] text-rose-600">
                                    <i data-lucide="circle-alert" class="size-3.5 shrink-0"></i><span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="label">Konfirmasi <span class="text-rose-500">*</span></label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required minlength="8"
                                   autocomplete="new-password" class="input" placeholder="ulangi password">
                        </div>
                    </div>

                    <button type="submit" id="btn-daftar" class="btn-primary w-full !py-3.5 text-base relative">
                        <i data-lucide="mail" class="size-[18px]"></i>
                        <span>Kirim Kode Verifikasi</span>
                    </button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-500">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Masuk di sini</a>
                </p>
            </div>

            <p class="text-center text-xs text-white/60 mt-6">
                &copy; {{ now()->year }} {{ config('simpas.nama_aplikasi') }} &middot; Ihya Media
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        // Konfirmasi password diceck sebelum dikirim supaya pesan muncul di
        // layar tanpa menunggu balasan server.
        var form = document.getElementById('form-daftar');
        if (!form) { return; }

        form.addEventListener('submit', function (e) {
            var a = document.getElementById('password').value;
            var b = document.getElementById('password_confirmation').value;

            if (a !== b) {
                e.preventDefault();
                alert('Konfirmasi password tidak sama.');
                return;
            }

            var btn = document.getElementById('btn-daftar');
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
        });
    })();
</script>
@endpush
</body>
</html>
