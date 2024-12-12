<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminStatistikKecamatanController extends Controller
{
    public function index($no_kec)
    {
        $data = DB::table('data_keluarga as dk')
            ->join('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->select(
                DB::raw('COUNT(dk.nama_kepala_keluarga) AS jumlah_kepala_keluarga'),
                DB::raw('SUM(dka.balita) AS total_balita'),
                DB::raw('SUM(dka.pus) AS total_pus'),
                DB::raw('SUM(dka.wus) AS total_wus'),
                DB::raw('SUM(dka.ibu_hamil) AS total_ibu_hamil'),
                DB::raw('SUM(dka.ibu_menyusui) AS total_ibu_menyusui'),
                DB::raw('SUM(dka.lansia) AS total_lansia'),
                DB::raw('SUM(dka.buta_baca) AS total_buta_baca'),
                DB::raw('SUM(dka.buta_tulis) AS total_buta_tulis'),
                DB::raw('SUM(dka.buta_hitung) AS total_buta_hitung'),
                DB::raw('SUM(dka.makanan_pokok) AS total_makanan_pokok'),
                DB::raw('COUNT(DISTINCT dka.makanan_pokok_lain) AS jumlah_makanan_pokok_lain'),
                DB::raw('SUM(dka.jamban_keluarga) AS total_jamban_keluarga'),
                DB::raw('SUM(dka.jamban_keluarga_jumlah) AS total_jamban_keluarga_jumlah'),
                DB::raw('SUM(dka.sumber_air_keluarga) AS total_sumber_air_keluarga'),
                DB::raw('COUNT(DISTINCT dka.sumber_air_keluarga_lain) AS jumlah_sumber_air_keluarga_lain'),
                DB::raw('SUM(dka.tempat_sampah_keluarga) AS total_tempat_sampah_keluarga'),
                DB::raw('SUM(dka.saluran_air_limbah) AS total_saluran_air_limbah'),
                DB::raw('SUM(dka.stiker_p4k) AS total_stiker_p4k'),
                DB::raw('SUM(dka.kriteria_rumah) AS total_kriteria_rumah'),
                DB::raw('SUM(dka.aktivitas_up2k) AS total_aktivitas_up2k'),
                DB::raw('COUNT(DISTINCT dka.aktivitas_up2k_lain) AS jumlah_aktivitas_up2k_lain'),
                DB::raw('SUM(dka.aktivitas_usaha_kesehatan_lingkungan) AS total_aktivitas_usaha_kesehatan_lingkungan'),
                DB::raw('SUM(dka.memiliki_tabungan) AS total_memiliki_tabungan')
            )
            ->where('dk.no_kec', $no_kec)
            ->groupBy('dk.no_kec')
            ->first();

        // Mengembalikan data ke view atau sebagai response JSON
        return view('admin.laporan.statistikkecamatan.index', compact('data'));
    }
}
