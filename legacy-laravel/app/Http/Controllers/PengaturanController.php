<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        return view('pengaturan.index', [
            'points' => config('simpas.points'),
            'auto' => config('simpas.auto_violation'),
            'pelanggaranTingkat' => config('simpas.pelanggaran_tingkat'),
            'master' => config('simpas.master'),
            'aktivitas' => config('simpas.activities'),
            'namaAplikasi' => config('simpas.nama_aplikasi'),
            'tagline' => config('simpas.tagline'),
        ]);
    }

    public function simpan(Request $request)
    {
        $this->requireAdmin();

        $request->validate([
            'points' => ['required', 'array'],
            'points.*' => ['required', 'integer', 'between:-10,10'],
        ]);

        Setting::set('points', json_encode($request->input('points')));
        Setting::set('auto_violation_enabled', $request->boolean('auto_enabled') ? '1' : '0');
        Setting::set('nama_aplikasi', $request->input('nama_aplikasi', 'SIMPAS'));

        // Terapkan langsung ke config berjalan
        config(['simpas.points' => $request->input('points')]);
        config(['simpas.auto_violation.enabled' => $request->boolean('auto_enabled')]);
        config(['simpas.nama_aplikasi' => $request->input('nama_aplikasi', 'SIMPAS')]);

        return redirect()->route('pengaturan.index')
            ->with('toast', ['type' => 'success', 'message' => 'Pengaturan berhasil disimpan.']);
    }
}
