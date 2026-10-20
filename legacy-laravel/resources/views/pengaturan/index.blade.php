@extends('layouts.app')
@section('title', 'Pengaturan')
@section('subtitle', 'Atur poin, otomatisasi, dan master data aplikasi')

@section('content')
<form method="POST" action="{{ route('pengaturan.simpan') }}" class="space-y-6">
    @csrf

    {{-- ====================== 1. POIN PER STATUS ====================== --}}
    <div class="card card-body animate-fade-up" data-reveal>
        <div class="flex items-center gap-3 mb-5">
            <div class="grid place-items-center size-10 rounded-xl bg-brand-50 border border-brand-100 text-brand-600">
                <i data-lucide="wallet" class="size-5"></i>
            </div>
            <div>
                <h2 class="font-extrabold text-slate-900 tracking-tight">Poin per Status</h2>
                <p class="text-xs text-slate-500">Berlaku untuk seluruh aktivitas kehadiran santri</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($points as $status => $nilai)
                @php
                    $dot = $status === 'Hadir' ? 'bg-emerald-500'
                        : ($status === 'Alfa' ? 'bg-rose-500' : 'bg-slate-400');
                    $teks = $status === 'Hadir' ? 'text-emerald-600'
                        : ($status === 'Alfa' ? 'text-rose-600' : 'text-slate-600');
                @endphp
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
                    <label class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-slate-600 mb-2" for="point-{{ \Illuminate\Support\Str::slug($status) }}">
                        <span class="size-2 rounded-full {{ $dot }}"></span>
                        <span class="{{ $teks }}">{{ $status }}</span>
                    </label>
                    <input id="point-{{ \Illuminate\Support\Str::slug($status) }}"
                           name="points[{{ $status }}]"
                           type="number" min="-10" max="10" step="1"
                           class="input text-center font-extrabold"
                           value="{{ old('points', $points)[$status] ?? $nilai }}">
                </div>
            @endforeach
        </div>
        <p class="hint">Rentang nilai -10 sampai 10. Poin negatif mengurangi perolehan santri.</p>
        @error('points')<p class="hint text-rose-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex justify-end no-print">
        <button type="submit" class="btn-primary">
            <i data-lucide="save" class="size-4"></i> Simpan Pengaturan
        </button>
    </div>

    {{-- ====================== 2. OTOMATISASI PELANGGARAN ====================== --}}
    <div class="card card-body animate-fade-up" data-reveal>
        <div class="flex items-center gap-3 mb-5">
            <div class="grid place-items-center size-10 rounded-xl bg-amber-50 border border-amber-100 text-amber-600">
                <i data-lucide="zap" class="size-5"></i>
            </div>
            <div>
                <h2 class="font-extrabold text-slate-900 tracking-tight">Otomatisasi Pelanggaran</h2>
                <p class="text-xs text-slate-500">Pelanggaran dibuat sistem saat batas alfa tercapai</p>
            </div>
        </div>

        <label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50/60 p-4 cursor-pointer select-none mb-4">
            <input type="checkbox" name="auto_enabled" value="1"
                   class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"
                   @checked(old('auto_enabled', $auto['enabled']))>
            <span class="text-sm font-bold text-slate-800">Aktifkan pembuatan pelanggaran otomatis</span>
            <span class="badge {{ $auto['enabled'] ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'badge-netral' }} !ml-auto">
                {{ $auto['enabled'] ? 'Aktif' : 'Nonaktif' }}
            </span>
        </label>

        <div class="table-wrap">
            <table class="table-simpas min-w-[480px]">
                <thead>
                    <tr>
                        <th>Aktivitas</th>
                        <th>Aturan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($aktivitas as $key => $act)
                        @php
                            $hari = $act['violation']['days'] ?? 0;
                            $periode = $hari === 7 ? '1 minggu' : ($hari === 30 ? '1 bulan' : $hari . ' hari');
                        @endphp
                        <tr>
                            <td class="font-semibold text-slate-800">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="{{ $act['icon'] }}" class="size-4 text-slate-400"></i>
                                    {{ $act['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($act['violation'])
                                    <span class="badge bg-amber-50 text-amber-700 border-amber-200">
                                        {{ $act['violation']['threshold'] }} Alfa / {{ $periode }}
                                    </span>
                                @else
                                    <span class="badge bg-rose-50 text-rose-700 border-rose-200">Tidak berlaku</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="hint">Pelanggaran otomatis disimpan dengan tingkat "{{ $auto['tingkat'] }}" dan poin {{ $auto['poin'] }}. Diniyah tidak memiliki aturan otomatisasi.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">

        {{-- ====================== 3. POIN PELANGGARAN PER TINGKAT ====================== --}}
        <div class="card card-body animate-fade-up" data-reveal>
            <div class="flex items-center gap-3 mb-5">
                <div class="grid place-items-center size-10 rounded-xl bg-rose-50 border border-rose-100 text-rose-600">
                    <i data-lucide="triangle-alert" class="size-5"></i>
                </div>
                <div>
                    <h2 class="font-extrabold text-slate-900 tracking-tight">Poin Pelanggaran per Tingkat</h2>
                    <p class="text-xs text-slate-500">Acuan baku, tidak dapat diubah</p>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                @foreach($pelanggaranTingkat as $tingkat => $nilai)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 text-center">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $tingkat }}</p>
                        <p class="mt-1 text-2xl font-extrabold text-rose-600">{{ $nilai }}</p>
                    </div>
                @endforeach
            </div>
            <p class="hint">Nilai otomatis disimpan negatif pada data pelanggaran.</p>
        </div>

        {{-- ====================== 4. INFO APLIKASI ====================== --}}
        <div class="card card-body animate-fade-up" data-reveal>
            <div class="flex items-center gap-3 mb-5">
                <div class="grid place-items-center size-10 rounded-xl bg-brand-50 border border-brand-100 text-brand-600">
                    <i data-lucide="info" class="size-5"></i>
                </div>
                <div>
                    <h2 class="font-extrabold text-slate-900 tracking-tight">Info Aplikasi</h2>
                    <p class="text-xs text-slate-500">Identitas tampilan aplikasi</p>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="label" for="nama_aplikasi">Nama Aplikasi</label>
                    <input id="nama_aplikasi" name="nama_aplikasi" type="text" class="input"
                           value="{{ old('nama_aplikasi', $namaAplikasi) }}" maxlength="50">
                    @error('nama_aplikasi')<p class="hint text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="label">Tagline</label>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-sm text-slate-600">
                        {{ $tagline }}
                    </div>
                
                </div>
            </div>
        </div>
    </div>

    {{-- ====================== 5. MASTER DATA ====================== --}}
    <div class="card card-body animate-fade-up" data-reveal>
        <div class="flex items-center gap-3 mb-5">
            <div class="grid place-items-center size-10 rounded-xl bg-brand-50 border border-brand-100 text-brand-600">
                <i data-lucide="database" class="size-5"></i>
            </div>
            <div>
                <h2 class="font-extrabold text-slate-900 tracking-tight">Master Data</h2>
                <p class="text-xs text-slate-500">Daftar baku yang digunakan seluruh modul (read-only)</p>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 stagger">
            @foreach([
                'Jenis Prestasi' => $master['jenis_prestasi'],
                'Tingkat Prestasi' => $master['tingkat_prestasi'],
                'Jenis Pelanggaran' => $master['jenis_pelanggaran'],
                'Tingkat Pelanggaran' => $master['tingkat_pelanggaran'],
                'Status Aktif' => $master['status_aktif'],
                'Pendidikan Formal' => $master['pendidikan_formal'],
                'Jenis Diniyah' => $master['jenis_diniyah'],
                'Level Diniyah' => $master['level_diniyah'],
                'Jenis Khatam' => $master['jenis_khatam'],
            ] as $judul => $daftar)
                <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-2.5">{{ $judul }}</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($daftar as $val)
                            <span class="badge badge-netral">{{ $val }}</span>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="flex justify-end no-print pb-4">
        <button type="submit" class="btn-primary">
            <i data-lucide="save" class="size-4"></i> Simpan Pengaturan
        </button>
    </div>
</form>
@endsection
