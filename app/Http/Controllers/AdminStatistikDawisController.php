<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataKeluargaAkumulasi;
use Illuminate\Support\Facades\DB;

class AdminStatistikDawisController extends Controller
{
    
    public function index($no_prop, $no_kab, $no_kec, $no_kel)
    {
        $data = DB::table('data_keluarga as dk')
        ->join('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk')
        ->select(
            'dk.no_kel',
            DB::raw('SUM(dka.balita) AS total_balita'),
            DB::raw('SUM(dka.pus) AS total_pus'),
            DB::raw('SUM(dka.wus) AS total_wus'),
            DB::raw('SUM(dka.ibu_hamil) AS total_ibu_hamil'),
            DB::raw('SUM(dka.ibu_menyusui) AS total_ibu_menyusui'),
            DB::raw('SUM(dka.lansia) AS total_lansia'),
            DB::raw('SUM(dka.buta_baca) AS total_buta_baca'),
            DB::raw('SUM(dka.buta_tulis) AS total_buta_tulis'),
            DB::raw('SUM(dka.buta_hitung) AS total_buta_hitung'),
            DB::raw('SUM(dka.jamban_keluarga_jumlah) AS total_jamban')
        )
        ->where('dk.no_kel', $no_kel)
        ->groupBy('dk.no_kel')
        ->orderBy('dk.no_kel')
        ->get();

        return view('admin.laporan.desa.statistikdesa.index',  compact('data'));
    }
}
