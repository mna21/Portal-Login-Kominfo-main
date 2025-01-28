<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kec; 
use App\Models\Kel;
use App\Models\DataKeluarga;
use App\Models\DataKeluargaAkumulasi;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Periksa namespace yang tepat

class SuperAdminLaporanDesaController extends Controller
{
    public function index($no_prop, $no_kab, $no_kec)
    {
        $dataPerDesa = DB::table('kel')
        ->leftJoin('data_keluarga as dk', function($join) {
            $join->on('kel.no_kel', '=', 'dk.no_kel')
                ->on('kel.no_kec', '=', 'dk.no_kec')
                ->on('kel.no_kab', '=', 'dk.no_kab')
                ->on('kel.no_prop', '=', 'dk.no_prop');
        })
        ->leftJoin('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk')
        ->select(
            'kel.no_kel',
            'kel.no_kec',
            'kel.no_kab',
            'kel.no_prop',
            'kel.nama_kel',
            DB::raw('COUNT(dk.nama_kepala_keluarga) AS jumlah_kepala_keluarga'),
            DB::raw('SUM(dka.jumlah_anggota_keluarga) AS total_jumlah_anggota_keluarga'),
            DB::raw('SUM(dka.balita) AS total_balita'),
            DB::raw('SUM(dka.pus) AS total_pus'),
            DB::raw('SUM(dka.wus) AS total_wus'),
            DB::raw('SUM(dka.ibu_hamil) AS total_ibu_hamil'),
            DB::raw('SUM(dka.ibu_menyusui) AS total_ibu_menyusui'),
            DB::raw('SUM(dka.lansia) AS total_lansia'),
            DB::raw('SUM(dka.buta_baca) AS total_buta_baca'),
            DB::raw('SUM(dka.buta_tulis) AS total_buta_tulis'),
            DB::raw('SUM(dka.buta_hitung) AS total_buta_hitung'),
            DB::raw('SUM(dka.difabel) AS total_difabel'),

            DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 1 THEN 1 ELSE 0 END) AS jumlah_layak_huni'), // Total Layak Huni
            DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 0 THEN 1 ELSE 0 END) AS jumlah_tidak_layak_huni'), // Total Tidak Layak Huni
            DB::raw('SUM(dka.tempat_sampah_keluarga) AS total_tempat_sampah_keluarga'),
            DB::raw('SUM(dka.saluran_air_limbah) AS total_saluran_air_limbah'),
            DB::raw('SUM(dka.jamban_keluarga) AS total_jamban_keluarga'),
            DB::raw('SUM(dka.jamban_keluarga_jumlah) AS total_jamban_keluarga_jumlah'),
            DB::raw('SUM(dka.stiker_p4k) AS total_stiker_p4k'),

            DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 1 THEN 1 ELSE 0 END) AS jumlah_pdam'),
            DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 2 THEN 1 ELSE 0 END) AS jumlah_sumur'),
            DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 3 THEN 1 ELSE 0 END) AS jumlah_sumber_air_lain'),

            //DB::raw('SUM(dka.sumber_air_keluarga) AS total_sumber_air_keluarga'),
            //DB::raw('COUNT(DISTINCT dka.sumber_air_keluarga_lain) AS jumlah_sumber_air_keluarga_lain'),

            DB::raw('SUM(CASE WHEN dka.makanan_pokok = 1 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok'),
            DB::raw('SUM(CASE WHEN dka.makanan_pokok = 2 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok_lain'),

            DB::raw('SUM(dka.aktivitas_up2k) AS total_aktivitas_up2k'),
            DB::raw('COUNT(DISTINCT dka.aktivitas_up2k_lain) AS jumlah_aktivitas_up2k_lain'),
            DB::raw('SUM(dka.memiliki_tabungan) AS total_memiliki_tabungan'),
            DB::raw('SUM(dka.aktivitas_usaha_kesehatan_lingkungan) AS total_aktivitas_usaha_kesehatan_lingkungan')
        )
        ->where('kel.no_kec', $no_kec)
        ->where('kel.no_kab', $no_kab)
        ->where('kel.no_prop', $no_prop)
        ->groupBy('kel.no_kel', 'kel.no_kec', 'kel.no_kab', 'kel.no_prop', 'kel.nama_kel')
        ->get();

        //dd($dataPerDesa);

        
        //Get Kecamatan Name
        $namaKec = DB::table('kec')
        ->where('no_prop', $no_prop)
        ->where('no_kab', $no_kab)
        ->where('no_kec', $no_kec)
        ->value('nama_kec');


        $totalKeseluruhan = DB::table('kel')
            ->leftJoin('data_keluarga as dk', function ($join) {
                $join->on('kel.no_kel', '=', 'dk.no_kel')
                    ->on('kel.no_kec', '=', 'dk.no_kec')
                    ->on('kel.no_kab', '=', 'dk.no_kab')
                    ->on('kel.no_prop', '=', 'dk.no_prop');
            })
            ->leftJoin('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->selectRaw('
                COUNT(dk.nama_kepala_keluarga) AS jumlah_kepala_keluarga,
                SUM(dka.jumlah_anggota_keluarga) AS total_jumlah_anggota_keluarga,
                SUM(dka.balita) AS total_balita,
                SUM(dka.pus) AS total_pus,
                SUM(dka.wus) AS total_wus,
                SUM(dka.ibu_hamil) AS total_ibu_hamil,
                SUM(dka.ibu_menyusui) AS total_ibu_menyusui,
                SUM(dka.lansia) AS total_lansia,
                SUM(dka.buta_baca) AS total_buta_baca,
                SUM(dka.buta_tulis) AS total_buta_tulis,
                SUM(dka.buta_hitung) AS total_buta_hitung,
                SUM(dka.difabel) AS total_difabel,

                SUM(CASE WHEN dka.kriteria_rumah = 1 THEN 1 ELSE 0 END) AS jumlah_layak_huni,
                SUM(CASE WHEN dka.kriteria_rumah = 0 THEN 1 ELSE 0 END) AS jumlah_tidak_layak_huni,
                SUM(dka.tempat_sampah_keluarga) AS total_tempat_sampah_keluarga,
                SUM(dka.saluran_air_limbah) AS total_saluran_air_limbah,
                SUM(dka.jamban_keluarga) AS total_jamban_keluarga,
                SUM(dka.jamban_keluarga_jumlah) AS total_jamban_keluarga_jumlah,
                SUM(dka.stiker_p4k) AS total_stiker_p4k,
                SUM(dka.sumber_air_keluarga) AS total_sumber_air_keluarga,
                SUM(CASE WHEN dka.sumber_air_keluarga = 1 THEN 1 ELSE 0 END) AS jumlah_pdam,
                SUM(CASE WHEN dka.sumber_air_keluarga = 2 THEN 1 ELSE 0 END) AS jumlah_sumur,
                SUM(CASE WHEN dka.sumber_air_keluarga = 3 THEN 1 ELSE 0 END) AS jumlah_sumber_air_lain,
                COUNT(DISTINCT dka.sumber_air_keluarga_lain) AS jumlah_sumber_air_keluarga_lain,
                SUM(CASE WHEN dka.makanan_pokok = 1 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok,
                SUM(CASE WHEN dka.makanan_pokok = 2 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok_lain,
                SUM(dka.aktivitas_up2k) AS total_aktivitas_up2k,
                COUNT(DISTINCT dka.aktivitas_up2k_lain) AS jumlah_aktivitas_up2k_lain,
                SUM(dka.memiliki_tabungan) AS total_memiliki_tabungan,
                SUM(dka.aktivitas_usaha_kesehatan_lingkungan) AS total_aktivitas_usaha_kesehatan_lingkungan
            ')
            ->where('kel.no_kec', $no_kec)
            ->where('kel.no_kab', $no_kab)
            ->where('kel.no_prop', $no_prop)
            ->first();
        
        //dd($totalKeseluruhan);

        // Pass the data to the view
        return view('superadmin.laporan.desa.index',[
            'dataPerDesa' => $dataPerDesa,
            'totalKeseluruhan' => $totalKeseluruhan,
            'namaKec'=> $namaKec
        ]);
    }


    public function statistik($no_prop, $no_kab, $no_kec)
    {
        // Total keseluruhan data dari query
        $totalKeseluruhan = DB::table('kel')
            ->leftJoin('data_keluarga as dk', function ($join) {
                $join->on('kel.no_kel', '=', 'dk.no_kel')
                    ->on('kel.no_kec', '=', 'dk.no_kec')
                    ->on('kel.no_kab', '=', 'dk.no_kab')
                    ->on('kel.no_prop', '=', 'dk.no_prop');
            })
            ->leftJoin('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->selectRaw('
                COUNT(dk.nama_kepala_keluarga) AS jumlah_kepala_keluarga,
                SUM(dka.jumlah_anggota_keluarga) AS total_jumlah_anggota_keluarga,
                SUM(dka.balita) AS total_balita,
                SUM(dka.pus) AS total_pus,
                SUM(dka.wus) AS total_wus,
                SUM(dka.ibu_hamil) AS total_ibu_hamil,
                SUM(dka.ibu_menyusui) AS total_ibu_menyusui,
                SUM(dka.lansia) AS total_lansia,
                SUM(dka.buta_baca) AS total_buta_baca,
                SUM(dka.buta_tulis) AS total_buta_tulis,
                SUM(dka.buta_hitung) AS total_buta_hitung,
                SUM(dka.difabel) AS total_difabel,
                
                SUM(CASE WHEN dka.kriteria_rumah = 1 THEN 1 ELSE 0 END) AS jumlah_layak_huni,
                SUM(CASE WHEN dka.kriteria_rumah = 0 THEN 1 ELSE 0 END) AS jumlah_tidak_layak_huni,
                SUM(dka.tempat_sampah_keluarga) AS total_tempat_sampah_keluarga,
                SUM(dka.saluran_air_limbah) AS total_saluran_air_limbah,
                SUM(dka.jamban_keluarga) AS total_jamban_keluarga,
                SUM(dka.jamban_keluarga_jumlah) AS total_jamban_keluarga_jumlah,
                SUM(dka.stiker_p4k) AS total_stiker_p4k,
                SUM(dka.sumber_air_keluarga) AS total_sumber_air_keluarga,
                SUM(CASE WHEN dka.sumber_air_keluarga = 1 THEN 1 ELSE 0 END) AS jumlah_pdam,
                SUM(CASE WHEN dka.sumber_air_keluarga = 2 THEN 1 ELSE 0 END) AS jumlah_sumur,
                SUM(CASE WHEN dka.sumber_air_keluarga = 3 THEN 1 ELSE 0 END) AS jumlah_sumber_air_lain,
                COUNT(DISTINCT dka.sumber_air_keluarga_lain) AS jumlah_sumber_air_keluarga_lain,
                SUM(CASE WHEN dka.makanan_pokok = 1 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok,
                SUM(CASE WHEN dka.makanan_pokok = 2 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok_lain,
                SUM(dka.aktivitas_up2k) AS total_aktivitas_up2k,
                COUNT(DISTINCT dka.aktivitas_up2k_lain) AS jumlah_aktivitas_up2k_lain,
                SUM(dka.memiliki_tabungan) AS total_memiliki_tabungan,
                SUM(dka.aktivitas_usaha_kesehatan_lingkungan) AS total_aktivitas_usaha_kesehatan_lingkungan
            ')
            ->where('kel.no_kec', $no_kec)
            ->where('kel.no_kab', $no_kab)
            ->where('kel.no_prop', $no_prop)
            ->first();
    
        // Prepare data for chart
        $chartData = [
            'labels' => [
                'Kepala Keluarga', 'Jumlah Anggota Keluarga', 'Balita', 'Pasangan Usia Subur', 'Wanita Usia Subur', 'Ibu Hamil', 'Ibu Menyusui', 'Lansia',
                'Buta Baca', 'Buta Tulis', 'Buta Hitung', 'Berkebutuhan Khusus', 'Layak Huni', 'Tidak Layak Huni', 'Tempat Sampah Keluarga',
                'Saluran Air Limbah', 'Jamban Keluarga', 'Jamban Keluarga Jumlah', 'Stiker P4K',
                'PDAM', 'Sumur','Sumber Air Lain', 'Beras', 'Non Beras',
                'Aktivitas UP2K', 'Aktivitas UP2K Lain', 'Memiliki Tabungan', 'Usaha Kesehatan Lingkungan'
            ],
            'values' => [
                $totalKeseluruhan->jumlah_kepala_keluarga,
                $totalKeseluruhan->total_jumlah_anggota_keluarga,
                $totalKeseluruhan->total_balita,
                $totalKeseluruhan->total_pus,
                $totalKeseluruhan->total_wus,
                $totalKeseluruhan->total_ibu_hamil,
                $totalKeseluruhan->total_ibu_menyusui,
                $totalKeseluruhan->total_lansia,
                $totalKeseluruhan->total_buta_baca,
                $totalKeseluruhan->total_buta_tulis,
                $totalKeseluruhan->total_buta_hitung,
                $totalKeseluruhan->total_difabel,

                $totalKeseluruhan->jumlah_layak_huni,
                $totalKeseluruhan->jumlah_tidak_layak_huni,
                $totalKeseluruhan->total_tempat_sampah_keluarga,
                $totalKeseluruhan->total_saluran_air_limbah,
                $totalKeseluruhan->total_jamban_keluarga,
                $totalKeseluruhan->total_jamban_keluarga_jumlah,
                $totalKeseluruhan->total_stiker_p4k,

                $totalKeseluruhan->jumlah_pdam,
                $totalKeseluruhan->jumlah_sumur,
                $totalKeseluruhan->jumlah_sumber_air_lain,

                $totalKeseluruhan->jumlah_makanan_pokok,
                $totalKeseluruhan->jumlah_makanan_pokok_lain,

                $totalKeseluruhan->total_aktivitas_up2k,
                $totalKeseluruhan->jumlah_aktivitas_up2k_lain,
                $totalKeseluruhan->total_memiliki_tabungan,
                $totalKeseluruhan->total_aktivitas_usaha_kesehatan_lingkungan
            ]
        ];

        //dd($chartData);

        $dataPerDesa = DB::table('kel')
        ->leftJoin('data_keluarga as dk', function($join) {
            $join->on('kel.no_kel', '=', 'dk.no_kel')
                ->on('kel.no_kec', '=', 'dk.no_kec')
                ->on('kel.no_kab', '=', 'dk.no_kab')
                ->on('kel.no_prop', '=', 'dk.no_prop');
        })
        ->leftJoin('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk')
        ->select(
            'kel.no_kel',
            'kel.no_kec',
            'kel.no_kab',
            'kel.no_prop',
            'kel.nama_kel',
            DB::raw('COUNT(dk.nama_kepala_keluarga) AS jumlah_kepala_keluarga'),
            DB::raw('SUM(dka.jumlah_anggota_keluarga) AS total_jumlah_anggota_keluarga'),
            DB::raw('SUM(dka.balita) AS total_balita'),
            DB::raw('SUM(dka.pus) AS total_pus'),
            DB::raw('SUM(dka.wus) AS total_wus'),
            DB::raw('SUM(dka.ibu_hamil) AS total_ibu_hamil'),
            DB::raw('SUM(dka.ibu_menyusui) AS total_ibu_menyusui'),
            DB::raw('SUM(dka.lansia) AS total_lansia'),
            DB::raw('SUM(dka.buta_baca) AS total_buta_baca'),
            DB::raw('SUM(dka.buta_tulis) AS total_buta_tulis'),
            DB::raw('SUM(dka.buta_hitung) AS total_buta_hitung'),
            DB::raw('SUM(dka.difabel) AS total_difabel'),

            DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 1 THEN 1 ELSE 0 END) AS jumlah_layak_huni'), // Total Layak Huni
            DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 0 THEN 1 ELSE 0 END) AS jumlah_tidak_layak_huni'), // Total Tidak Layak Huni
            DB::raw('SUM(dka.tempat_sampah_keluarga) AS total_tempat_sampah_keluarga'),
            DB::raw('SUM(dka.saluran_air_limbah) AS total_saluran_air_limbah'),
            DB::raw('SUM(dka.jamban_keluarga) AS total_jamban_keluarga'),
            DB::raw('SUM(dka.jamban_keluarga_jumlah) AS total_jamban_keluarga_jumlah'),
            DB::raw('SUM(dka.stiker_p4k) AS total_stiker_p4k'),

            DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 1 THEN 1 ELSE 0 END) AS jumlah_pdam'),
            DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 2 THEN 1 ELSE 0 END) AS jumlah_sumur'),
            DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 3 THEN 1 ELSE 0 END) AS jumlah_sumber_air_lain'),

            //DB::raw('SUM(dka.sumber_air_keluarga) AS total_sumber_air_keluarga'),
            //DB::raw('COUNT(DISTINCT dka.sumber_air_keluarga_lain) AS jumlah_sumber_air_keluarga_lain'),

            DB::raw('SUM(CASE WHEN dka.makanan_pokok = 1 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok'),
            DB::raw('SUM(CASE WHEN dka.makanan_pokok = 2 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok_lain'),

            DB::raw('SUM(dka.aktivitas_up2k) AS total_aktivitas_up2k'),
            DB::raw('COUNT(DISTINCT dka.aktivitas_up2k_lain) AS jumlah_aktivitas_up2k_lain'),
            DB::raw('SUM(dka.memiliki_tabungan) AS total_memiliki_tabungan'),
            DB::raw('SUM(dka.aktivitas_usaha_kesehatan_lingkungan) AS total_aktivitas_usaha_kesehatan_lingkungan')
        )
        ->where('kel.no_kec', $no_kec)
        ->where('kel.no_kab', $no_kab)
        ->where('kel.no_prop', $no_prop)
        ->groupBy('kel.no_kel', 'kel.no_kec', 'kel.no_kab', 'kel.no_prop', 'kel.nama_kel')
        ->get();


        // Prepare data for chart bar
        $chartDataDesaPerItem = [
            'labels' => $dataPerDesa->pluck('nama_kel'), // Desa labels
            'datasets' => [
                [
                    'label' => 'Jumlah Kepala Keluarga',
                    'data' => $dataPerDesa->pluck('jumlah_kepala_keluarga'),
                    'backgroundColor' => '#4e73df', // Blue
                    'hoverBackgroundColor' => '#2e59d9',
                    'borderColor' => '#4e73df',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarJumlahKepalaKeluarga'
                ],
                [
                    'label' => 'Jumlah Anggota Keluarga',
                    'data' => $dataPerDesa->pluck('total_jumlah_anggota_keluarga'),
                    'backgroundColor' => '#4e73df', // Blue
                    'hoverBackgroundColor' => '#2e59d9',
                    'borderColor' => '#4e73df',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarJumlahAnggotaKeluarga'
                ],
                [
                    'label' => 'Balita',
                    'data' => $dataPerDesa->pluck('total_balita'),
                    'backgroundColor' => '#ff6347', // Tomato Red
                    'hoverBackgroundColor' => '#e55347',
                    'borderColor' => '#ff6347',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarBalita'
                ],
                [
                    'label' => 'Pasangan Usia Subur',
                    'data' => $dataPerDesa->pluck('total_pus'),
                    'backgroundColor' => '#28a745', // Green
                    'hoverBackgroundColor' => '#218838',
                    'borderColor' => '#28a745',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarPus'
                ],
                [
                    'label' => 'Wanita Usia Subur',
                    'data' => $dataPerDesa->pluck('total_wus'),
                    'backgroundColor' => '#17a2b8', // Teal
                    'hoverBackgroundColor' => '#138496',
                    'borderColor' => '#17a2b8',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarWus'
                ],
                [
                    'label' => 'Ibu Hamil',
                    'data' => $dataPerDesa->pluck('total_ibu_hamil'),
                    'backgroundColor' => '#ffc107', // Yellow
                    'hoverBackgroundColor' => '#e0a800',
                    'borderColor' => '#ffc107',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarIbuHamil'
                ],
                [
                    'label' => 'Ibu Menyusui',
                    'data' => $dataPerDesa->pluck('total_ibu_menyusui'),
                    'backgroundColor' => '#ffc107', // Yellow
                    'hoverBackgroundColor' => '#e0a800',
                    'borderColor' => '#ffc107',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarIbuMenyusui'
                ],
                [
                    'label' => 'Lansia',
                    'data' => $dataPerDesa->pluck('total_lansia'),
                    'backgroundColor' => '#e74a3b', // Red
                    'hoverBackgroundColor' => '#d23f31',
                    'borderColor' => '#e74a3b',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarLansia'
                ],
                [
                    'label' => 'Buta Baca',
                    'data' => $dataPerDesa->pluck('total_buta_baca'),
                    'backgroundColor' => '#f56b00', // Dark Orange
                    'hoverBackgroundColor' => '#e45d00',
                    'borderColor' => '#f56b00',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarButaBaca'
                ],
                [
                    'label' => 'Buta Tulis',
                    'data' => $dataPerDesa->pluck('total_buta_tulis'),
                    'backgroundColor' => '#e67e22', // Orange
                    'hoverBackgroundColor' => '#d55d20',
                    'borderColor' => '#e67e22',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarButaTulis'
                ],
                [
                    'label' => 'Buta Hitung',
                    'data' => $dataPerDesa->pluck('total_buta_hitung'),
                    'backgroundColor' => '#34495e', // Grey-Blue
                    'hoverBackgroundColor' => '#2c3e50',
                    'borderColor' => '#34495e',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarButaHitung'
                ],
                [
                    'label' => 'Berkebutuhan Khusus',
                    'data' => $dataPerDesa->pluck('total_difabel'),
                    'backgroundColor' => '#34495e', // Grey-Blue
                    'hoverBackgroundColor' => '#2c3e50',
                    'borderColor' => '#34495e',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarDifabel'
                ],
                [
                    'label' => 'Rumah Layak Huni',
                    'data' => $dataPerDesa->pluck('jumlah_layak_huni'),
                    'backgroundColor' => '#8e44ad', // Purple
                    'hoverBackgroundColor' => '#7d3c96',
                    'borderColor' => '#8e44ad',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarKriteriaRumahLayak'
                ],
                [
                    'label' => 'Rumah Tidak Layak Huni',
                    'data' => $dataPerDesa->pluck('jumlah_tidak_layak_huni'),
                    'backgroundColor' => '#c0392b', // Red
                    'hoverBackgroundColor' => '#a93226',
                    'borderColor' => '#c0392b',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarKriteriaRumahTidakLayak'
                ],
                [
                    'label' => 'Tempat Sampah Keluarga',
                    'data' => $dataPerDesa->pluck('total_tempat_sampah_keluarga'),
                    'backgroundColor' => '#2ecc71', // Green
                    'hoverBackgroundColor' => '#27ae60',
                    'borderColor' => '#2ecc71',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarTempatSampahKeluarga'
                ],
                [
                    'label' => 'Saluran Air Limbah',
                    'data' => $dataPerDesa->pluck('total_saluran_air_limbah'),
                    'backgroundColor' => '#3498db', // Light Blue
                    'hoverBackgroundColor' => '#2980b9',
                    'borderColor' => '#3498db',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarSaluranAirLimbah'
                ],
                [
                    'label' => 'Jamban Keluarga',
                    'data' => $dataPerDesa->pluck('total_jamban_keluarga'),
                    'backgroundColor' => '#f39c12', // Gold
                    'hoverBackgroundColor' => '#e67e22',
                    'borderColor' => '#f39c12',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarJambanKeluarga'
                ],
                [
                    'label' => 'Jamban Keluarga Jumlah',
                    'data' => $dataPerDesa->pluck('total_jamban_keluarga_jumlah'),
                    'backgroundColor' => '#f39c12', // Gold
                    'hoverBackgroundColor' => '#e67e22',
                    'borderColor' => '#f39c12',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarJambanKeluargaJumlah'
                ],
                [
                    'label' => 'Stiker P4K',
                    'data' => $dataPerDesa->pluck('total_stiker_p4k'),
                    'backgroundColor' => '#e74c3c', // Light Red
                    'hoverBackgroundColor' => '#c0392b',
                    'borderColor' => '#e74c3c',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarStikerP4K'
                ],
                [
                    'label' => 'PDAM',
                    'data' => $dataPerDesa->pluck('jumlah_pdam'),
                    'backgroundColor' => '#2ecc71', // Green
                    'hoverBackgroundColor' => '#27ae60',
                    'borderColor' => '#2ecc71',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarPDAM'
                ],
                [
                    'label' => 'Sumur',
                    'data' => $dataPerDesa->pluck('jumlah_sumur'),
                    'backgroundColor' => '#e67e22', // Orange
                    'hoverBackgroundColor' => '#d55d20',
                    'borderColor' => '#e67e22',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarSumur'
                ],
                [
                    'label' => 'Sumber Air Lain',
                    'data' => $dataPerDesa->pluck('jumlah_sumber_air_lain'),
                    'backgroundColor' => '#2ecc71', // Green
                    'hoverBackgroundColor' => '#27ae60',
                    'borderColor' => '#2ecc71',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarSumberAirLain'
                ],
                [
                    'label' => 'Beras',
                    'data' => $dataPerDesa->pluck('jumlah_makanan_pokok'),
                    'backgroundColor' => '#36b9cc',
                    'hoverBackgroundColor' => '#2c9faf',
                    'borderColor' => '#36b9cc',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarMakananPokok' // Dynamic chart ID for Makanan Pokok
                ],
                [
                    'label' => 'Non Beras',
                    'data' => $dataPerDesa->pluck('jumlah_makanan_pokok_lain'),
                    'backgroundColor' => '#36b9cc',
                    'hoverBackgroundColor' => '#2c9faf',
                    'borderColor' => '#36b9cc',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarMakananPokokLain' // Dynamic chart ID for Makanan Pokok
                ],
                [
                    'label' => 'Aktivitas UP2K',
                    'data' => $dataPerDesa->pluck('total_aktivitas_up2k'),
                    'backgroundColor' => '#f6c23e',
                    'hoverBackgroundColor' => '#e0a800',
                    'borderColor' => '#f6c23e',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarAktivitasUP2K' // Dynamic chart ID for Aktivitas UP2K
                ],
                [
                    'label' => 'Aktivitas Usaha Kesehatan Lingkungan',
                    'data' => $dataPerDesa->pluck('total_aktivitas_usaha_kesehatan_lingkungan'),
                    'backgroundColor' => '#e74a3b',
                    'hoverBackgroundColor' => '#d23f31',
                    'borderColor' => '#e74a3b',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarAktivitasUsahaKesehatanLingkungan' // Dynamic chart ID for Aktivitas Usaha Kesehatan Lingkungan
                ]
            ],
        ];
    
        // Returning the data to the view
        return view('superadmin.laporan.desa.statistikkecamatan', [
            'chartData' => $chartData,
            'chartDataDesaPerItem' => $chartDataDesaPerItem,
            'totalKeseluruhan' => $totalKeseluruhan,
            'no_prop' => $no_prop,
            'no_kab' => $no_kab,
            'no_kec' => $no_kec,
        ]);
    }


    public function downloadPdf($no_prop, $no_kab, $no_kec)
    {
        // Query untuk data per desa
        $dataPerDesa = DB::table('kel')
            ->leftJoin('data_keluarga as dk', function ($join) {
                $join->on('kel.no_kel', '=', 'dk.no_kel')
                    ->on('kel.no_kec', '=', 'dk.no_kec')
                    ->on('kel.no_kab', '=', 'dk.no_kab')
                    ->on('kel.no_prop', '=', 'dk.no_prop');
            })
            ->leftJoin('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->select(
                'kel.no_kel',
                'kel.no_kec',
                'kel.no_kab',
                'kel.no_prop',
                'kel.nama_kel',
                DB::raw('COUNT(dk.nama_kepala_keluarga) AS jumlah_kepala_keluarga'),
                DB::raw('SUM(dka.balita) AS total_balita'),
                DB::raw('SUM(dka.jumlah_anggota_keluarga) AS total_jumlah_anggota_keluarga'),
                DB::raw('SUM(dka.balita) AS total_balita'),
                DB::raw('SUM(dka.pus) AS total_pus'),
                DB::raw('SUM(dka.wus) AS total_wus'),
                DB::raw('SUM(dka.ibu_hamil) AS total_ibu_hamil'),
                DB::raw('SUM(dka.ibu_menyusui) AS total_ibu_menyusui'),
                DB::raw('SUM(dka.lansia) AS total_lansia'),
                DB::raw('SUM(dka.buta_baca) AS total_buta_baca'),
                DB::raw('SUM(dka.buta_tulis) AS total_buta_tulis'),
                DB::raw('SUM(dka.buta_hitung) AS total_buta_hitung'),
                DB::raw('SUM(dka.difabel) AS total_difabel'),
    
                DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 1 THEN 1 ELSE 0 END) AS jumlah_layak_huni'), // Total Layak Huni
                DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 0 THEN 1 ELSE 0 END) AS jumlah_tidak_layak_huni'), // Total Tidak Layak Huni
                DB::raw('SUM(dka.tempat_sampah_keluarga) AS total_tempat_sampah_keluarga'),
                DB::raw('SUM(dka.saluran_air_limbah) AS total_saluran_air_limbah'),
                DB::raw('SUM(dka.jamban_keluarga) AS total_jamban_keluarga'),
                DB::raw('SUM(dka.jamban_keluarga_jumlah) AS total_jamban_keluarga_jumlah'),
                DB::raw('SUM(dka.stiker_p4k) AS total_stiker_p4k'),
    
                DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 1 THEN 1 ELSE 0 END) AS jumlah_pdam'),
                DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 2 THEN 1 ELSE 0 END) AS jumlah_sumur'),
                DB::raw('SUM(CASE WHEN dka.sumber_air_keluarga = 3 THEN 1 ELSE 0 END) AS jumlah_sumber_air_lain'),
    
                //DB::raw('SUM(dka.sumber_air_keluarga) AS total_sumber_air_keluarga'),
                //DB::raw('COUNT(DISTINCT dka.sumber_air_keluarga_lain) AS jumlah_sumber_air_keluarga_lain'),
    
                DB::raw('SUM(CASE WHEN dka.makanan_pokok = 1 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok'),
                DB::raw('SUM(CASE WHEN dka.makanan_pokok = 2 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok_lain'),
    
                DB::raw('SUM(dka.aktivitas_up2k) AS total_aktivitas_up2k'),
                DB::raw('COUNT(DISTINCT dka.aktivitas_up2k_lain) AS jumlah_aktivitas_up2k_lain'),
                DB::raw('SUM(dka.memiliki_tabungan) AS total_memiliki_tabungan'),
                DB::raw('SUM(dka.aktivitas_usaha_kesehatan_lingkungan) AS total_aktivitas_usaha_kesehatan_lingkungan')
            )
            ->where('kel.no_kec', $no_kec)
            ->where('kel.no_kab', $no_kab)
            ->where('kel.no_prop', $no_prop)
            ->groupBy('kel.no_kel', 'kel.no_kec', 'kel.no_kab', 'kel.no_prop', 'kel.nama_kel')
            ->get();
    
        // Query total keseluruhan
        $totalKeseluruhan = DB::table('kel')
            ->leftJoin('data_keluarga as dk', function ($join) {
                $join->on('kel.no_kel', '=', 'dk.no_kel')
                    ->on('kel.no_kec', '=', 'dk.no_kec')
                    ->on('kel.no_kab', '=', 'dk.no_kab')
                    ->on('kel.no_prop', '=', 'dk.no_prop');
            })
            ->leftJoin('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->selectRaw('
                COUNT(dk.nama_kepala_keluarga) AS jumlah_kepala_keluarga,
                SUM(dka.jumlah_anggota_keluarga) AS total_jumlah_anggota_keluarga,
                SUM(dka.balita) AS total_balita,
                SUM(dka.pus) AS total_pus,
                SUM(dka.wus) AS total_wus,
                SUM(dka.ibu_hamil) AS total_ibu_hamil,
                SUM(dka.ibu_menyusui) AS total_ibu_menyusui,
                SUM(dka.lansia) AS total_lansia,
                SUM(dka.buta_baca) AS total_buta_baca,
                SUM(dka.buta_tulis) AS total_buta_tulis,
                SUM(dka.buta_hitung) AS total_buta_hitung,
                SUM(dka.difabel) AS total_difabel,

                SUM(CASE WHEN dka.kriteria_rumah = 1 THEN 1 ELSE 0 END) AS jumlah_layak_huni,
                SUM(CASE WHEN dka.kriteria_rumah = 0 THEN 1 ELSE 0 END) AS jumlah_tidak_layak_huni,
                SUM(dka.tempat_sampah_keluarga) AS total_tempat_sampah_keluarga,
                SUM(dka.saluran_air_limbah) AS total_saluran_air_limbah,
                SUM(dka.jamban_keluarga) AS total_jamban_keluarga,
                SUM(dka.jamban_keluarga_jumlah) AS total_jamban_keluarga_jumlah,
                SUM(dka.stiker_p4k) AS total_stiker_p4k,
                SUM(dka.sumber_air_keluarga) AS total_sumber_air_keluarga,
                SUM(CASE WHEN dka.sumber_air_keluarga = 1 THEN 1 ELSE 0 END) AS jumlah_pdam,
                SUM(CASE WHEN dka.sumber_air_keluarga = 2 THEN 1 ELSE 0 END) AS jumlah_sumur,
                SUM(CASE WHEN dka.sumber_air_keluarga = 3 THEN 1 ELSE 0 END) AS jumlah_sumber_air_lain,
                COUNT(DISTINCT dka.sumber_air_keluarga_lain) AS jumlah_sumber_air_keluarga_lain,
                SUM(CASE WHEN dka.makanan_pokok = 1 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok,
                SUM(CASE WHEN dka.makanan_pokok = 2 THEN 1 ELSE 0 END) AS jumlah_makanan_pokok_lain,
                SUM(dka.aktivitas_up2k) AS total_aktivitas_up2k,
                COUNT(DISTINCT dka.aktivitas_up2k_lain) AS jumlah_aktivitas_up2k_lain,
                SUM(dka.memiliki_tabungan) AS total_memiliki_tabungan,
                SUM(dka.aktivitas_usaha_kesehatan_lingkungan) AS total_aktivitas_usaha_kesehatan_lingkungan
            ')
            ->where('kel.no_kec', $no_kec)
            ->where('kel.no_kab', $no_kab)
            ->where('kel.no_prop', $no_prop)
            ->first();
    
        // Load PDF view
        $pdf = PDF::loadView('superadmin.laporan.desa.laporanDesPDF', [
            'dataPerDesa' => $dataPerDesa,
            'totalKeseluruhan' => $totalKeseluruhan
        ])->setPaper('A4', 'landscape'); // Atur kertas landscape
    
        return $pdf->download('laporan_Desa_PerKecamatan.pdf');
    }
}




