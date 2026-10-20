@extends('layouts.app')

@section('title', 'Kelola Akun · ' . config('simpas.nama_aplikasi'))

@section('content')
<div class="space-y-6">

    {{-- ============ HEADER ============ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 animate-fade-down">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kelola Akun Pengguna</h1>
            <p class="text-sm text-slate-500 mt-1">
                Tidak ada pendaftaran mandiri. Akun dibuat di sini oleh Admin/Pengurus.
            </p>
        </div>
        <a href="{{ route('akun.create') }}" class="btn-primary">
            <i data-lucide="user-plus" class="size-4"></i>
            <span>Buat Akun</span>
        </a>
    </div>

    {{-- ============ KARTU STATISTIK ============ --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 animate-fade-up">
        @foreach([
            ['label' => 'Total Akun', 'nilai' => $statistik['total'],    'icon' => 'users',          'warna' => 'text-slate-600 bg-slate-100'],
            ['label' => 'Admin/Pengurus', 'nilai' => $statistik['admin'], 'icon' => 'shield-check',   'warna' => 'text-emerald-700 bg-emerald-100'],
            ['label' => 'Wali Santri', 'nilai' => $statistik['wali'],      'icon' => 'user-check',     'warna' => 'text-amber-700 bg-amber-100'],
            ['label' => 'Santri', 'nilai' => $statistik['santri'],        'icon' => 'graduation-cap', 'warna' => 'text-indigo-700 bg-indigo-100'],
            ['label' => 'Nonaktif', 'nilai' => $statistik['nonaktif'],    'icon' => 'user-x',         'warna' => 'text-rose-700 bg-rose-100'],
        ] as $s)
            <div class="card p-4 flex items-center gap-3">
                <span class="grid place-items-center size-10 rounded-xl {{ $s['warna'] }} shrink-0">
                    <i data-lucide="{{ $s['icon'] }}" class="size-5"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-xl font-extrabold text-slate-900 tabular">{{ $s['nilai'] }}</p>
                    <p class="text-[11px] text-slate-500 truncate">{{ $s['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ============ FILTER ============ --}}
    <div class="card p-4 animate-fade-up">
        <form method="GET" action="{{ route('akun.index') }}" class="flex flex-col sm:flex-row gap-2">
            <div class="relative flex-1">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 size-4 text-slate-400 pointer-events-none"></i>
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="Cari nama atau kode akun..." class="input !pl-10">
            </div>

            <select name="peran" class="input sm:w-44">
                <option value="">Semua Peran</option>
                @foreach(['admin' => 'Admin/Pengurus', 'wali' => 'Wali Santri', 'santri' => 'Santri'] as $v => $l)
                    <option value="{{ $v }}" @selected(request('peran') === $v)>{{ $l }}</option>
                @endforeach
            </select>

            <select name="status" class="input sm:w-36">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
            </select>

            <button type="submit" class="btn-secondary">Filter</button>

            @if(request()->hasAny(['q', 'peran', 'status']))
                <a href="{{ route('akun.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- ============ TABEL AKUN ============ --}}
    <div class="card overflow-hidden animate-fade-up">
        @if($akun->isEmpty())
            <div class="p-12 text-center">
                <i data-lucide="user-x" class="size-10 text-slate-300 mx-auto mb-3"></i>
                <p class="font-semibold text-slate-600">Tidak ada akun yang cocok</p>
                <p class="text-sm text-slate-500 mt-1">Ubah kata kunci filter atau buat akun baru.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Akun</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Peran</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Santri Tertaut</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-600">Login Terakhir</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-600">Status</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-600">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($akun as $a)
                            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50/70">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="grid place-items-center size-9 rounded-full bg-brand-100 text-brand-700 font-bold text-xs shrink-0">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($a->name, 0, 2)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-800 truncate">{{ $a->name }}</p>
                                            <p class="text-xs text-slate-500 tabular">{{ $a->username }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold',
                                        'bg-emerald-100 text-emerald-700' => $a->role === 'admin',
                                        'bg-amber-100 text-amber-700' => $a->role === 'wali',
                                        'bg-indigo-100 text-indigo-700' => $a->role === 'santri',
                                    ])>{{ $a->peran_label }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @php $anakUtama = $a->santris->first(); @endphp
                                    @if($a->isAdmin())
                                        <span class="text-slate-400">&mdash;</span>
                                        <p class="text-xs text-slate-400">
                                            {{ $a->santris->count() > 1 ? $a->santris->count() . ' anak tertaut' : 'Tidak punya anak tertaut' }}
                                        </p>
                                    @elseif($anakUtama)
                                        <a href="{{ route('santri.show', $anakUtama) }}" class="text-brand-700 hover:underline">
                                            {{ $anakUtama->nama }}
                                        </a>
                                        <p class="text-xs text-slate-500 tabular">NIS {{ $anakUtama->nis }}</p>
                                        @if($a->santris->count() > 1)
                                            {{-- Satu wali bisa punya beberapa anak; tampilkan jumlahnya. --}}
                                            <p class="text-xs font-medium text-emerald-700">
                                                +{{ $a->santris->count() - 1 }} anak lain
                                            </p>
                                        @endif
                                    @else
                                        {{-- Wali tanpa relasi tidak boleh ada; ditampilkan agar terlihat. --}}
                                        <span class="text-rose-600 font-medium">Belum tertaut</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500">
                                    @if($a->last_login_at)
                                        {{ $a->last_login_at->translatedFormat('d M Y H:i') }}
                                    @else
                                        <span class="text-slate-400">Belum pernah</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span @class([
                                        'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold',
                                        'bg-emerald-100 text-emerald-700' => $a->isActive(),
                                        'bg-rose-100 text-rose-700' => !$a->isActive(),
                                    ])>
                                        <i data-lucide="{{ $a->isActive() ? 'check' : 'x' }}" class="size-3"></i>
                                        {{ $a->isActive() ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('akun.edit', $a) }}" class="btn-secondary !px-2.5 !py-1.5 text-xs"
                                           aria-label="Ubah akun {{ $a->username }}">
                                            <i data-lucide="pencil" class="size-3.5"></i>
                                        </a>

                                        {{-- Nonaktifkan/aktifkan --}}
                                        <form method="POST" action="{{ route('akun.toggle', $a) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="{{ $a->isActive() ? 'btn-secondary' : 'btn-primary' }} !px-2.5 !py-1.5 text-xs"
                                                    aria-label="{{ $a->isActive() ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $a->username }}">
                                                <i data-lucide="{{ $a->isActive() ? 'user-x' : 'user-check' }}" class="size-3.5"></i>
                                            </button>
                                        </form>

                                        {{-- Atur ulang password --}}
                                        <button type="button"
                                                class="btn-secondary !px-2.5 !py-1.5 text-xs"
                                                data-reset-akun="{{ route('akun.password', $a) }}"
                                                data-nama="{{ $a->name }}"
                                                aria-label="Atur ulang password {{ $a->username }}">
                                            <i data-lucide="key-round" class="size-3.5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-slate-100">
                {{ $akun->links() }}
            </div>
        @endif
    </div>

    <p class="text-xs text-slate-500">
        Menonaktifkan akun langsung menutup aksesnya, bahkan bila sesinya masih terbuka di perangkat lain.
        Akun tidak dihapus dari basis data agar riwayat aktivitas tetap terhubung.
    </p>
</div>

{{-- ============ MODAL ATUR ULANG PASSWORD ============ --}}
<div id="modal-password" class="hidden fixed inset-0 z-50 items-center justify-center p-4 bg-slate-900/50">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="modal-password-title">
        <h3 id="modal-password-title" class="text-lg font-bold text-slate-900">Atur Ulang Password</h3>
        <p class="text-sm text-slate-500 mt-1">
            Password baru untuk akun <strong id="modal-password-nama" class="text-slate-700"></strong>.
            Sesi aktif akun tersebut langsung tidak berlaku.
        </p>

        <form method="POST" id="form-password" class="mt-4 space-y-4">
            @csrf
            <div>
                <label for="pw-baru" class="label">Password Baru</label>
                <input id="pw-baru" type="password" name="password" required minlength="8"
                       autocomplete="new-password" class="input" placeholder="minimal 8 karakter">
            </div>
            <div>
                <label for="pw-konfirmasi" class="label">Konfirmasi Password</label>
                <input id="pw-konfirmasi" type="password" name="password_confirmation" required minlength="8"
                       autocomplete="new-password" class="input" placeholder="ulangi password baru">
            </div>

            <div id="modal-password-error" class="hidden rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700"></div>

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" id="modal-password-batal" class="btn-secondary">Batal</button>
                <button type="submit" class="btn-primary">Simpan Password</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        var modal = document.getElementById('modal-password');
        var form = document.getElementById('form-password');
        var nama = document.getElementById('modal-password-nama');
        var boxError = document.getElementById('modal-password-error');

        function buka(url, namaAkun) {
            form.action = url;
            nama.textContent = namaAkun;
            boxError.classList.add('hidden');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.getElementById('pw-baru').focus();
        }

        function tutup() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            form.reset();
        }

        document.querySelectorAll('[data-reset-akun]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                buka(tombol.dataset.resetAkun, tombol.dataset.nama);
            });
        });

        document.getElementById('modal-password-batal').addEventListener('click', tutup);
        modal.addEventListener('click', function (e) { if (e.target === modal) tutup(); });

        // Password dan konfirmasi harus sama sebelum dikirim.
        form.addEventListener('submit', function (e) {
            var a = document.getElementById('pw-baru').value;
            var b = document.getElementById('pw-konfirmasi').value;
            if (a !== b) {
                e.preventDefault();
                boxError.textContent = 'Konfirmasi password tidak sama.';
                boxError.classList.remove('hidden');
            }
        });
    })();
</script>
@endpush
@endsection
