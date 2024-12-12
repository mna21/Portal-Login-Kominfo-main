<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kab;
use App\Models\Kec;  // Pastikan ada model Kecamatan
use App\Models\Kel;
use App\Models\DataKeluarga;
use App\Models\DataKeluargaAkumulasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminLaporanKecamatanController extends Controller
{
    public function index()
    {
        $dataPerKecamatan = DB::table('kec as kec')
        ->leftJoin('data_keluarga as dk', 'dk.no_kec', '=', 'kec.no_kec') // Menggunakan leftJoin
        ->leftJoin('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk') // Menggunakan leftJoin
        ->select(
            'kec.no_kec',  // Tambahkan no_kec
            'kec.no_kab',  // Tambahkan no_kab
            'kec.no_prop', // Tambahkan no_prop
            'kec.nama_kec', // Ambil nama kecamatan
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

            DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 1 THEN 1 ELSE 0 END) AS jumlah_layak_huni'), // Layak huni
            DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 0 THEN 1 ELSE 0 END) AS jumlah_tidak_layak_huni'), // Tidak layak huni
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
        ->groupBy('kec.no_kec', 'kec.no_kab', 'kec.no_prop', 'kec.nama_kec') // Kelompokkan berdasarkan kecamatan
        ->get();
    

        // Hitung total keseluruhan dari semua kecamatan
        $totalKeseluruhan = DB::table('data_keluarga as dk')
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
            ->first();

            //dd($totalKeseluruhan);

            return view('admin.laporan.index', [
                'dataPerKecamatan' => $dataPerKecamatan,
                'totalKeseluruhan' => $totalKeseluruhan
            ]);
    }


    public function statistik()
    {

       // Hitung total keseluruhan dari semua kecamatan
        $totalKeseluruhan = DB::table('data_keluarga as dk')
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
        ->first();

        // Menyiapkan data untuk chart
        $dataChartBarTotalAll = [
            'labels' => ['Jumlah Kepala Keluarga', 'Balita', 'PUS', 'WUS', 'Ibu Hamil', 'Ibu Menyusui','Lansia', 'Buta Baca', 'Buta Tulis', 'Buta Hitung', 'Layak Huni', 'Tidak Layak Huni', 'Tempat Sampah Keluarga', 'Saluran Air Limbah', 'Jamban Keluarga', 'Jumlah Jamban Keluarga',  'Stiker P4K','PDAM', 'SUMUR', 'Sumber Air Lain', 'Makanan Pokok', 'Jumlah Makanan Pokok Lain', 'Aktivitas UP2K', 'Jumlah Aktivitas UP2K Lain', 'Memiliki Tabungan', 'Aktivitas Usaha Kesehatan Lingkungan'],
            'data' => [
                $totalKeseluruhan->jumlah_kepala_keluarga,
                $totalKeseluruhan->total_balita,
                $totalKeseluruhan->total_pus,
                $totalKeseluruhan->total_wus,
                $totalKeseluruhan->total_ibu_hamil,
                $totalKeseluruhan->total_ibu_menyusui,
                $totalKeseluruhan->total_lansia,
                $totalKeseluruhan->total_buta_baca,
                $totalKeseluruhan->total_buta_tulis,
                $totalKeseluruhan->total_buta_hitung,

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


        $dataPerKecamatan = DB::table('kec as kec')
            ->leftJoin('data_keluarga as dk', 'dk.no_kec', '=', 'kec.no_kec') // Menggunakan leftJoin
            ->leftJoin('data_keluarga_akumulasi as dka', 'dk.no_kk', '=', 'dka.no_kk') // Menggunakan leftJoin
            ->select(
                'kec.no_kec',  // Tambahkan no_kec
                'kec.no_kab',  // Tambahkan no_kab
                'kec.no_prop', // Tambahkan no_prop
                'kec.nama_kec', // Ambil nama kecamatan
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

                DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 1 THEN 1 ELSE 0 END) AS jumlah_layak_huni'), // Layak huni
                DB::raw('SUM(CASE WHEN dka.kriteria_rumah = 0 THEN 1 ELSE 0 END) AS jumlah_tidak_layak_huni'), // Tidak layak huni
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
            ->groupBy('kec.no_kec', 'kec.no_kab', 'kec.no_prop', 'kec.nama_kec') // Kelompokkan berdasarkan kecamatan
            ->get();

            $dataChartBarPerItem = [
                'labels' => $dataPerKecamatan->pluck('nama_kec'),
                'datasets' => [
                    [
                        'label' => 'Jumlah Kepala Keluarga',
                        'data' => $dataPerKecamatan->pluck('jumlah_kepala_keluarga'),
                        'backgroundColor' => '#4e73df', // Blue
                        'hoverBackgroundColor' => '#2e59d9',
                        'borderColor' => '#4e73df',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarJumlahKepalaKeluarga'
                    ],
                    [
                        'label' => 'Balita',
                        'data' => $dataPerKecamatan->pluck('total_balita'),
                        'backgroundColor' => '#ff6347', // Tomato Red
                        'hoverBackgroundColor' => '#e55347',
                        'borderColor' => '#ff6347',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarBalita'
                    ],
                    [
                        'label' => 'PUS',
                        'data' => $dataPerKecamatan->pluck('total_pus'),
                        'backgroundColor' => '#28a745', // Green
                        'hoverBackgroundColor' => '#218838',
                        'borderColor' => '#28a745',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarPus'
                    ],
                    [
                        'label' => 'WUS',
                        'data' => $dataPerKecamatan->pluck('total_wus'),
                        'backgroundColor' => '#17a2b8', // Teal
                        'hoverBackgroundColor' => '#138496',
                        'borderColor' => '#17a2b8',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarWus'
                    ],
                    [
                        'label' => 'Ibu Hamil',
                        'data' => $dataPerKecamatan->pluck('total_ibu_hamil'),
                        'backgroundColor' => '#ffc107', // Yellow
                        'hoverBackgroundColor' => '#e0a800',
                        'borderColor' => '#ffc107',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarIbuHamil'
                    ],
                    [
                        'label' => 'Ibu Menyusui',
                        'data' => $dataPerKecamatan->pluck('total_ibu_menyusui'),
                        'backgroundColor' => '#ffc107', // Yellow
                        'hoverBackgroundColor' => '#e0a800',
                        'borderColor' => '#ffc107',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarIbuMenyusui'
                    ],
                    [
                        'label' => 'Lansia',
                        'data' => $dataPerKecamatan->pluck('total_lansia'),
                        'backgroundColor' => '#e74a3b', // Red
                        'hoverBackgroundColor' => '#d23f31',
                        'borderColor' => '#e74a3b',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarLansia'
                    ],
                    [
                        'label' => 'Buta Baca',
                        'data' => $dataPerKecamatan->pluck('total_buta_baca'),
                        'backgroundColor' => '#f56b00', // Dark Orange
                        'hoverBackgroundColor' => '#e45d00',
                        'borderColor' => '#f56b00',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarButaBaca'
                    ],
                    [
                        'label' => 'Buta Tulis',
                        'data' => $dataPerKecamatan->pluck('total_buta_tulis'),
                        'backgroundColor' => '#e67e22', // Orange
                        'hoverBackgroundColor' => '#d55d20',
                        'borderColor' => '#e67e22',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarButaTulis'
                    ],
                    [
                        'label' => 'Buta Hitung',
                        'data' => $dataPerKecamatan->pluck('total_buta_hitung'),
                        'backgroundColor' => '#34495e', // Grey-Blue
                        'hoverBackgroundColor' => '#2c3e50',
                        'borderColor' => '#34495e',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarButaHitung'
                    ],
                    [
                        'label' => 'Rumah Layak Huni',
                        'data' => $dataPerKecamatan->pluck('jumlah_layak_huni'),
                        'backgroundColor' => '#8e44ad', // Purple
                        'hoverBackgroundColor' => '#7d3c96',
                        'borderColor' => '#8e44ad',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarKriteriaRumahLayak'
                    ],
                    [
                        'label' => 'Rumah Tidak Layak Huni',
                        'data' => $dataPerKecamatan->pluck('jumlah_tidak_layak_huni'),
                        'backgroundColor' => '#c0392b', // Red
                        'hoverBackgroundColor' => '#a93226',
                        'borderColor' => '#c0392b',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarKriteriaRumahTidakLayak'
                    ],
                    [
                        'label' => 'Tempat Sampah Keluarga',
                        'data' => $dataPerKecamatan->pluck('total_tempat_sampah_keluarga'),
                        'backgroundColor' => '#2ecc71', // Green
                        'hoverBackgroundColor' => '#27ae60',
                        'borderColor' => '#2ecc71',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarTempatSampahKeluarga'
                    ],
                    [
                        'label' => 'Saluran Air Limbah',
                        'data' => $dataPerKecamatan->pluck('total_saluran_air_limbah'),
                        'backgroundColor' => '#3498db', // Light Blue
                        'hoverBackgroundColor' => '#2980b9',
                        'borderColor' => '#3498db',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarSaluranAirLimbah'
                    ],
                    [
                        'label' => 'Jamban Keluarga',
                        'data' => $dataPerKecamatan->pluck('total_jamban_keluarga'),
                        'backgroundColor' => '#f39c12', // Gold
                        'hoverBackgroundColor' => '#e67e22',
                        'borderColor' => '#f39c12',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarJambanKeluarga'
                    ],
                    [
                        'label' => 'Jamban Keluarga Jumlah',
                        'data' => $dataPerKecamatan->pluck('total_jamban_keluarga_jumlah'),
                        'backgroundColor' => '#f39c12', // Gold
                        'hoverBackgroundColor' => '#e67e22',
                        'borderColor' => '#f39c12',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarJambanKeluargaJumlah'
                    ],
                    [
                        'label' => 'Stiker P4K',
                        'data' => $dataPerKecamatan->pluck('total_stiker_p4k'),
                        'backgroundColor' => '#e74c3c', // Light Red
                        'hoverBackgroundColor' => '#c0392b',
                        'borderColor' => '#e74c3c',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarStikerP4K'
                    ],
                    [
                        'label' => 'PDAM',
                        'data' => $dataPerKecamatan->pluck('jumlah_pdam'),
                        'backgroundColor' => '#2ecc71', // Green
                        'hoverBackgroundColor' => '#27ae60',
                        'borderColor' => '#2ecc71',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarPDAM'
                    ],
                    [
                        'label' => 'Sumur',
                        'data' => $dataPerKecamatan->pluck('jumlah_sumur'),
                        'backgroundColor' => '#e67e22', // Orange
                        'hoverBackgroundColor' => '#d55d20',
                        'borderColor' => '#e67e22',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarSumur'
                    ],
                    [
                        'label' => 'Sumber Air Lain',
                        'data' => $dataPerKecamatan->pluck('jumlah_sumber_air_lain'),
                        'backgroundColor' => '#2ecc71', // Green
                        'hoverBackgroundColor' => '#27ae60',
                        'borderColor' => '#2ecc71',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarSumberAirLain'
                    ],
                    [
                        'label' => 'Beras',
                        'data' => $dataPerKecamatan->pluck('jumlah_makanan_pokok'),
                        'backgroundColor' => '#36b9cc',
                        'hoverBackgroundColor' => '#2c9faf',
                        'borderColor' => '#36b9cc',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarMakananPokok' // Dynamic chart ID for Makanan Pokok
                    ],
                    [
                        'label' => 'Non Beras',
                        'data' => $dataPerKecamatan->pluck('jumlah_makanan_pokok_lain'),
                        'backgroundColor' => '#36b9cc',
                        'hoverBackgroundColor' => '#2c9faf',
                        'borderColor' => '#36b9cc',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarMakananPokokLain' // Dynamic chart ID for Makanan Pokok
                    ],
                    [
                        'label' => 'Aktivitas UP2K',
                        'data' => $dataPerKecamatan->pluck('total_aktivitas_up2k'),
                        'backgroundColor' => '#f6c23e',
                        'hoverBackgroundColor' => '#e0a800',
                        'borderColor' => '#f6c23e',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarAktivitasUP2K' // Dynamic chart ID for Aktivitas UP2K
                    ],
                    [
                        'label' => 'Aktivitas Usaha Kesehatan Lingkungan',
                        'data' => $dataPerKecamatan->pluck('total_aktivitas_usaha_kesehatan_lingkungan'),
                        'backgroundColor' => '#e74a3b',
                        'hoverBackgroundColor' => '#d23f31',
                        'borderColor' => '#e74a3b',
                        'borderWidth' => 1,
                        'chartId' => 'chartBarAktivitasUsahaKesehatanLingkungan' // Dynamic chart ID for Aktivitas Usaha Kesehatan Lingkungan
                    ]
                ]
            ];
            
            //dd($dataChartBarTotalAll);
        
        return view('admin.laporan.statistik', [
            'dataChartBarTotalAll' => $dataChartBarTotalAll,
            'dataChartBarPerItem' => $dataChartBarPerItem,
        ]);
    }
}
