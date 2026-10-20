<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#059669">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Verifikasi Email · {{ config('simpas.nama_aplikasi') }}</title>

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

<div class="min-h-screen grid place-items-center p-6 sm:p-10 relative z-10">
    <div class="w-full max-w-lg">

        <div class="flex items-center justify-center gap-3 mb-8 animate-fade-down">
            <img src="/images/logo-pondok.png" alt="Logo Pondok" class="size-10 rounded-2xl drop-shadow-xl">
            <p class="font-extrabold text-white text-xl leading-none">SIMPAS</p>
        </div>

        <div class="rounded-3xl bg-white/95 backdrop-blur-xl border border-white/30
                    shadow-2xl shadow-slate-900/10 p-7 sm:p-9 animate-fade-up">

            <div class="text-center mb-7">
                <span class="inline-grid place-items-center size-14 rounded-2xl bg-emerald-50 border border-emerald-100 mb-4">
                    <i data-lucide="shield-check" class="size-7 text-emerald-600"></i>
                </span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Verifikasi Email</h1>
                <p class="text-sm text-slate-500 mt-2">
                    Masukkan kode yang kami kirim ke email Anda.
                </p>
            </div>

            {{-- Email tujuan --}}
            <div class="mb-5 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Email tujuan</p>
                <p class="mt-1 font-semibold text-slate-800 break-all">{{ $email ?: '—' }}</p>
            </div>

            {{--
                Mode pengembangan: MAIL_MAILER=log menulis kode ke
                storage/logs/laravel.log, bukan mengirimnya. Kode ditampilkan di
                sini supaya alur tetap bisa dicoba. Blok ini hilang sendiri begitu
                MAIL_MAILER diganti ke smtp.
            --}}
            @if(session('kode_dev'))
                <div class="mb-5 rounded-2xl border border-amber-300 bg-amber-50 p-4">
                    <p class="text-[11px] font-bold text-amber-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="flask-conical" class="size-3.5"></i>
                        Mode Pengembangan
                    </p>
                    <p class="text-sm text-amber-900 mt-1.5">
                        Email belum benar-benar terkirim. Kode Anda:
                    </p>
                    <p class="mt-2 text-center text-3xl font-extrabold tracking-[.3em] text-amber-900 font-mono">
                        {{ session('kode_dev') }}
                    </p>
                </div>
            @endif

            {{-- Daftar anak yang otomatis tertaut --}}
            @if($jumlahAnak > 0)
                <div class="mb-5 rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4">
                    <p class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider flex items-center gap-1.5">
                        <i data-lucide="users" class="size-3.5"></i>
                        {{ $jumlahAnak }} Anak Terhubung
                    </p>
                    <ul class="mt-2 space-y-1.5">
                        @foreach($anak as $s)
                            <li class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-emerald-900">{{ $s->nama }}</span>
                                <span class="tabular text-xs text-emerald-700">{{ $s->nis }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-2.5 text-[12px] text-emerald-800">
                        Nomor WhatsApp Anda cocok dengan data {{ $jumlahAnak }} Santri, semuanya ditautkan ke akun ini.
                    </p>
                </div>
            @elseif($email)
                <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 p-4">
                    <p class="text-sm text-amber-900">
                        Nomor WhatsApp yang dipakai belum terdaftar sebagai nomor wali.
                        Akun tetap dibuat, tetapi data anak perlu dihubungkan oleh Admin/Pengurus.
                    </p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700 animate-scale-in" role="alert">
                    <div class="flex items-start gap-2">
                        <i data-lucide="circle-alert" class="size-4 mt-0.5 shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('daftar.verifikasi') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label for="kode" class="label">Kode Verifikasi <span class="text-rose-500">*</span></label>
                    <input id="kode" type="text" name="kode" required inputmode="numeric" autocomplete="one-time-code"
                           maxlength="{{ config('simpas.registration.otp.length', 6) }}"
                           pattern="[0-9]*"
                           class="input text-center !text-2xl tracking-[.45em] font-mono font-bold"
                           placeholder="{{ str_repeat('0', config('simpas.registration.otp.length', 6)) }}">
                    <p class="mt-1.5 text-[12px] text-slate-500">
                        Berlaku {{ config('simpas.registration.otp.ttl', 15) }} menit, hanya bisa dipakai sekali.
                    </p>
                </div>

                <button type="submit" class="btn-primary w-full !py-3.5 text-base">
                    <i data-lucide="shield-check" class="size-[18px]"></i>
                    <span>Verifikasi Sekarang</span>
                </button>
            </form>

            <div class="mt-5 flex items-center justify-between text-sm">
                <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-700">
                    <i data-lucide="arrow-left" class="size-3.5 inline"></i> Kembali ke masuk
                </a>

                <form method="POST" action="{{ route('daftar.verifikasi.kirim-ulang') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="font-semibold text-brand-700 hover:underline">
                        Kirim ulang kode
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center text-xs text-white/60 mt-6">
            &copy; {{ now()->year }} {{ config('simpas.nama_aplikasi') }} &middot; Ihya Media
        </p>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        // Kolom kode hanya menerima angka, dan berpindah ke bawah sendiri
        // setelah terisi penuh.
        var kode = document.getElementById('kode');
        if (!kode) { return; }

        kode.addEventListener('input', function () {
            kode.value = kode.value.replace(/\D+/g, '');
        });

        kode.addEventListener('paste', function () {
            setTimeout(function () {
                kode.value = kode.value.replace(/\D+/g, '').slice(0, 6);
            }, 0);
        });
    })();
</script>
@endpush
</body>
</html>
