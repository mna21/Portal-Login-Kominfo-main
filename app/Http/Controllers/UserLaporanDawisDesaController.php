<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kec;
use App\Models\Kel;
use App\Models\DataKeluarga;
use App\Models\Dawis;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf; // Periksa namespace yang tepat

class UserLaporanDawisDesaController extends Controller
{

    public function index(Request $request)
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Pastikan user memiliki akses wilayah
        if (!$user->no_kel || !$user->no_kec || !$user->no_kab || !$user->no_prop) {
            return redirect()->back()->with('error', 'Akun Anda tidak memiliki akses ke data wilayah tertentu.');
        }

        // Query untuk mengambil data sesuai dengan wilayah user
        $dawisData = DB::table('data_keluarga AS dk')
            ->join('data_keluarga_akumulasi AS dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->join('dawis AS dw', 'dk.dawis_id', '=', 'dw.id')
            ->select(
                'dw.id AS dawis_id',
                'dw.nama_dawis',
                'dk.no_kel',
                'dk.no_kec',
                'dk.no_kab',
                'dk.no_prop',
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
            ->where('dk.no_prop', $user->no_prop)
            ->where('dk.no_kab', $user->no_kab)
            ->where('dk.no_kec', $user->no_kec)
            ->where('dk.no_kel', $user->no_kel)
            ->groupBy('dw.id', 'dw.nama_dawis', 'dk.no_kel', 'dk.no_kec', 'dk.no_kab', 'dk.no_prop')
            ->get();
        //dd($dawisData);
        
            // Calculate totals
        $totals = [
            'jumlah_kepala_keluarga' => $dawisData->sum('jumlah_kepala_keluarga'),
            'total_jumlah_anggota_keluarga' => $dawisData->sum('total_jumlah_anggota_keluarga'),
            'total_balita' => $dawisData->sum('total_balita'),
            'total_pus' => $dawisData->sum('total_pus'),
            'total_wus' => $dawisData->sum('total_wus'),
            'total_ibu_hamil' => $dawisData->sum('total_ibu_hamil'),
            'total_ibu_menyusui' => $dawisData->sum('total_ibu_menyusui'),
            'total_lansia' => $dawisData->sum('total_lansia'),
            'total_buta_baca' => $dawisData->sum('total_buta_baca'),
            'total_buta_tulis' => $dawisData->sum('total_buta_tulis'),
            'total_buta_hitung' => $dawisData->sum('total_buta_hitung'),
            'total_difabel' => $dawisData->sum('total_difabel'),
            'jumlah_layak_huni' => $dawisData->sum('jumlah_layak_huni'),
            'jumlah_tidak_layak_huni' => $dawisData->sum('jumlah_tidak_layak_huni'),
            'total_tempat_sampah_keluarga' => $dawisData->sum('total_tempat_sampah_keluarga'),
            'total_saluran_air_limbah' => $dawisData->sum('total_saluran_air_limbah'),
            'total_jamban_keluarga' => $dawisData->sum('total_jamban_keluarga'),
            'total_jamban_keluarga_jumlah' => $dawisData->sum('total_jamban_keluarga_jumlah'),
            'total_stiker_p4k' => $dawisData->sum('total_stiker_p4k'),
            'jumlah_pdam' => $dawisData->sum('jumlah_pdam'),
            'jumlah_sumur' => $dawisData->sum('jumlah_sumur'),
            'jumlah_sumber_air_lain' => $dawisData->sum('jumlah_sumber_air_lain'),
            'jumlah_makanan_pokok' => $dawisData->sum('jumlah_makanan_pokok'),
            'jumlah_makanan_pokok_lain' => $dawisData->sum('jumlah_makanan_pokok_lain'),
            'total_aktivitas_up2k' => $dawisData->sum('total_aktivitas_up2k'),
            'jumlah_aktivitas_up2k_lain' => $dawisData->sum('jumlah_aktivitas_up2k_lain'),
            'total_memiliki_tabungan' => $dawisData->sum('total_memiliki_tabungan'),
            'total_aktivitas_usaha_kesehatan_lingkungan' => $dawisData->sum('total_aktivitas_usaha_kesehatan_lingkungan'),
        ];

        //dd($totals);

        // Return ke view laporan
        return view('user.dasawisma.laporan.index', [
            'dawisData'=>$dawisData,
            'totals' =>$totals
        ]);
    }

    public function statistikDesa()
    {
        // Ambil user yang sedang login
        $user = auth()->user();

        // Pastikan user memiliki akses wilayah
        if (!$user->no_kel || !$user->no_kec || !$user->no_kab || !$user->no_prop) {
            return redirect()->back()->with('error', 'Akun Anda tidak memiliki akses ke data wilayah tertentu.');
        }

        // Query untuk mengambil data sesuai dengan wilayah user
        $dawisData = DB::table('data_keluarga AS dk')
            ->join('data_keluarga_akumulasi AS dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->join('dawis AS dw', 'dk.dawis_id', '=', 'dw.id')
            ->select(
                'dw.id AS dawis_id',
                'dw.nama_dawis',
                'dk.no_kel',
                'dk.no_kec',
                'dk.no_kab',
                'dk.no_prop',
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
            ->where('dk.no_prop', $user->no_prop)
            ->where('dk.no_kab', $user->no_kab)
            ->where('dk.no_kec', $user->no_kec)
            ->where('dk.no_kel', $user->no_kel)
            ->groupBy('dw.id', 'dw.nama_dawis', 'dk.no_kel', 'dk.no_kec', 'dk.no_kab', 'dk.no_prop')
            ->get();

        
            // Calculate totals
        $totals = [
            'jumlah_kepala_keluarga' => $dawisData->sum('jumlah_kepala_keluarga'),
            'total_jumlah_anggota_keluarga' => $dawisData->sum('total_jumlah_anggota_keluarga'),
            'total_balita' => $dawisData->sum('total_balita'),
            'total_pus' => $dawisData->sum('total_pus'),
            'total_wus' => $dawisData->sum('total_wus'),
            'total_ibu_hamil' => $dawisData->sum('total_ibu_hamil'),
            'total_ibu_menyusui' => $dawisData->sum('total_ibu_menyusui'),
            'total_lansia' => $dawisData->sum('total_lansia'),
            'total_buta_baca' => $dawisData->sum('total_buta_baca'),
            'total_buta_tulis' => $dawisData->sum('total_buta_tulis'),
            'total_buta_hitung' => $dawisData->sum('total_buta_hitung'),
            'total_difabel' => $dawisData->sum('total_difabel'),
            'jumlah_layak_huni' => $dawisData->sum('jumlah_layak_huni'),
            'jumlah_tidak_layak_huni' => $dawisData->sum('jumlah_tidak_layak_huni'),
            'total_tempat_sampah_keluarga' => $dawisData->sum('total_tempat_sampah_keluarga'),
            'total_saluran_air_limbah' => $dawisData->sum('total_saluran_air_limbah'),
            'total_jamban_keluarga' => $dawisData->sum('total_jamban_keluarga'),
            'total_jamban_keluarga_jumlah' => $dawisData->sum('total_jamban_keluarga_jumlah'),
            'total_stiker_p4k' => $dawisData->sum('total_stiker_p4k'),
            'jumlah_pdam' => $dawisData->sum('jumlah_pdam'),
            'jumlah_sumur' => $dawisData->sum('jumlah_sumur'),
            'jumlah_sumber_air_lain' => $dawisData->sum('jumlah_sumber_air_lain'),
            'jumlah_makanan_pokok' => $dawisData->sum('jumlah_makanan_pokok'),
            'jumlah_makanan_pokok_lain' => $dawisData->sum('jumlah_makanan_pokok_lain'),
            'total_aktivitas_up2k' => $dawisData->sum('total_aktivitas_up2k'),
            'jumlah_aktivitas_up2k_lain' => $dawisData->sum('jumlah_aktivitas_up2k_lain'),
            'total_memiliki_tabungan' => $dawisData->sum('total_memiliki_tabungan'),
            'total_aktivitas_usaha_kesehatan_lingkungan' => $dawisData->sum('total_aktivitas_usaha_kesehatan_lingkungan'),
        ];


        $chartData = [
            'labels' => [
                'Kepala Keluarga', 'Jumlah Anggota Keluarga', 'Balita', 'PUS', 'WUS', 'Ibu Hamil', 'Ibu Menyusui', 'Lansia',
                'Buta Baca', 'Buta Tulis', 'Buta Hitung','Berkebutuhan Khusus', 'Layak Huni', 'Tidak Layak Huni', 'Tempat Sampah Keluarga',
                'Saluran Air Limbah', 'Jamban Keluarga', 'Jamban Keluarga Jumlah', 'Stiker P4K', 'PDAM', 'Sumur','Sumber Air Lain', 'Beras', 'Non Beras',
                'Aktivitas UP2K', 'Aktivitas UP2K Lain', 'Memiliki Tabungan', 'Usaha Kesehatan Lingkungan'
            ],
            'values' => [
                $totals['jumlah_kepala_keluarga'],
                $totals['total_jumlah_anggota_keluarga'],
                $totals['total_balita'],
                $totals['total_pus'],
                $totals['total_wus'],
                $totals['total_ibu_hamil'],
                $totals['total_ibu_menyusui'],
                $totals['total_lansia'],
                $totals['total_buta_baca'],
                $totals['total_buta_tulis'],
                $totals['total_buta_hitung'],
                $totals['total_difabel'],

                $totals['jumlah_layak_huni'],
                $totals['jumlah_tidak_layak_huni'],
                $totals['total_tempat_sampah_keluarga'],
                $totals['total_saluran_air_limbah'],
                $totals['total_jamban_keluarga'],
                $totals['total_jamban_keluarga_jumlah'],
                $totals['total_stiker_p4k'],
                
                $totals['jumlah_pdam'],
                $totals['jumlah_sumur'],
                $totals['jumlah_sumber_air_lain'],
                
                $totals['jumlah_makanan_pokok'],
                $totals['jumlah_makanan_pokok_lain'],
                
                $totals['total_aktivitas_up2k'],
                $totals['jumlah_aktivitas_up2k_lain'],
                $totals['total_memiliki_tabungan'],
                $totals['total_aktivitas_usaha_kesehatan_lingkungan']
            ]
        ];

        

        //dd($chartData);

        // Query untuk mengambil data sesuai dengan wilayah user
        $dawisDataDesa = DB::table('data_keluarga AS dk')
            ->join('data_keluarga_akumulasi AS dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->join('dawis AS dw', 'dk.dawis_id', '=', 'dw.id')
            ->select(
                'dw.id AS dawis_id',
                'dw.nama_dawis',
                'dk.no_kel',
                'dk.no_kec',
                'dk.no_kab',
                'dk.no_prop',
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
            ->where('dk.no_prop', $user->no_prop)
            ->where('dk.no_kab', $user->no_kab)
            ->where('dk.no_kec', $user->no_kec)
            ->where('dk.no_kel', $user->no_kel)
            ->groupBy('dw.id', 'dw.nama_dawis', 'dk.no_kel', 'dk.no_kec', 'dk.no_kab', 'dk.no_prop')
            ->get();

        // Prepare data for chart bar
        $chartDataDawisPerItem = [
            'labels' => $dawisDataDesa->pluck('nama_dawis'), // Desa labels
            'datasets' => [
                [
                    'label' => 'Jumlah Kepala Keluarga',
                    'data' => $dawisDataDesa->pluck('jumlah_kepala_keluarga'),
                    'backgroundColor' => '#4e73ef', // Blue
                    'hoverBackgroundColor' => '#2e59d9',
                    'borderColor' => '#4e73df',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarJumlahKepalaKeluarga'
                ],
                [
                    'label' => 'Jumlah Anggota Keluarga',
                    'data' => $dawisDataDesa->pluck('total_jumlah_anggota_keluarga'),
                    'backgroundColor' => '#4e73df', // Blue
                    'hoverBackgroundColor' => '#2e59d9',
                    'borderColor' => '#4e73df',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarJumlahAnggotaKeluarga'
                ],
                [
                    'label' => 'Balita',
                    'data' => $dawisDataDesa->pluck('total_balita'),
                    'backgroundColor' => '#ff6347', // Tomato Red
                    'hoverBackgroundColor' => '#e55347',
                    'borderColor' => '#ff6347',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarBalita'
                ],
                [
                    'label' => 'PUS',
                    'data' => $dawisDataDesa->pluck('total_pus'),
                    'backgroundColor' => '#28a745', // Green
                    'hoverBackgroundColor' => '#218838',
                    'borderColor' => '#28a745',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarPus'
                ],
                [
                    'label' => 'WUS',
                    'data' => $dawisDataDesa->pluck('total_wus'),
                    'backgroundColor' => '#17a2b8', // Teal
                    'hoverBackgroundColor' => '#138496',
                    'borderColor' => '#17a2b8',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarWus'
                ],
                [
                    'label' => 'Ibu Hamil',
                    'data' => $dawisDataDesa->pluck('total_ibu_hamil'),
                    'backgroundColor' => '#ffc107', // Yellow
                    'hoverBackgroundColor' => '#e0a800',
                    'borderColor' => '#ffc107',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarIbuHamil'
                ],
                [
                    'label' => 'Ibu Menyusui',
                    'data' => $dawisDataDesa->pluck('total_ibu_menyusui'),
                    'backgroundColor' => '#ffc107', // Yellow
                    'hoverBackgroundColor' => '#e0a800',
                    'borderColor' => '#ffc107',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarIbuMenyusui'
                ],
                [
                    'label' => 'Lansia',
                    'data' => $dawisDataDesa->pluck('total_lansia'),
                    'backgroundColor' => '#e74a3b', // Red
                    'hoverBackgroundColor' => '#d23f31',
                    'borderColor' => '#e74a3b',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarLansia'
                ],
                [
                    'label' => 'Buta Baca',
                    'data' => $dawisDataDesa->pluck('total_buta_baca'),
                    'backgroundColor' => '#f56b00', // Dark Orange
                    'hoverBackgroundColor' => '#e45d00',
                    'borderColor' => '#f56b00',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarButaBaca'
                ],
                [
                    'label' => 'Buta Tulis',
                    'data' => $dawisDataDesa->pluck('total_buta_tulis'),
                    'backgroundColor' => '#e67e22', // Orange
                    'hoverBackgroundColor' => '#d55d20',
                    'borderColor' => '#e67e22',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarButaTulis'
                ],
                [
                    'label' => 'Buta Hitung',
                    'data' => $dawisDataDesa->pluck('total_buta_hitung'),
                    'backgroundColor' => '#34495e', // Grey-Blue
                    'hoverBackgroundColor' => '#2c3e50',
                    'borderColor' => '#34495e',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarButaHitung'
                ],
                [
                    'label' => 'Berkebutuhan Khusus',
                    'data' => $dawisDataDesa->pluck('total_difabel'),
                    'backgroundColor' => '#34495e', // Grey-Blue
                    'hoverBackgroundColor' => '#2c3e50',
                    'borderColor' => '#34495e',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarDifabel'
                ],
                [
                    'label' => 'Rumah Layak Huni',
                    'data' => $dawisDataDesa->pluck('jumlah_layak_huni'),
                    'backgroundColor' => '#8e44ad', // Purple
                    'hoverBackgroundColor' => '#7d3c96',
                    'borderColor' => '#8e44ad',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarKriteriaRumahLayak'
                ],
                [
                    'label' => 'Rumah Tidak Layak Huni',
                    'data' => $dawisDataDesa->pluck('jumlah_tidak_layak_huni'),
                    'backgroundColor' => '#c0392b', // Red
                    'hoverBackgroundColor' => '#a93226',
                    'borderColor' => '#c0392b',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarKriteriaRumahTidakLayak'
                ],
                [
                    'label' => 'Tempat Sampah Keluarga',
                    'data' => $dawisDataDesa->pluck('total_tempat_sampah_keluarga'),
                    'backgroundColor' => '#2ecc71', // Green
                    'hoverBackgroundColor' => '#27ae60',
                    'borderColor' => '#2ecc71',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarTempatSampahKeluarga'
                ],
                [
                    'label' => 'Saluran Air Limbah',
                    'data' => $dawisDataDesa->pluck('total_saluran_air_limbah'),
                    'backgroundColor' => '#3498db', // Light Blue
                    'hoverBackgroundColor' => '#2980b9',
                    'borderColor' => '#3498db',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarSaluranAirLimbah'
                ],
                [
                    'label' => 'Jamban Keluarga',
                    'data' => $dawisDataDesa->pluck('total_jamban_keluarga'),
                    'backgroundColor' => '#f39c12', // Gold
                    'hoverBackgroundColor' => '#e67e22',
                    'borderColor' => '#f39c12',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarJambanKeluarga'
                ],
                [
                    'label' => 'Jamban Keluarga Jumlah',
                    'data' => $dawisDataDesa->pluck('total_jamban_keluarga_jumlah'),
                    'backgroundColor' => '#f39c12', // Gold
                    'hoverBackgroundColor' => '#e67e22',
                    'borderColor' => '#f39c12',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarJambanKeluargaJumlah'
                ],
                [
                    'label' => 'Stiker P4K',
                    'data' => $dawisDataDesa->pluck('total_stiker_p4k'),
                    'backgroundColor' => '#e74c3c', // Light Red
                    'hoverBackgroundColor' => '#c0392b',
                    'borderColor' => '#e74c3c',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarStikerP4K'
                ],
                [
                    'label' => 'PDAM',
                    'data' => $dawisDataDesa->pluck('jumlah_pdam'),
                    'backgroundColor' => '#2ecc71', // Green
                    'hoverBackgroundColor' => '#27ae60',
                    'borderColor' => '#2ecc71',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarPDAM'
                ],
                [
                    'label' => 'Sumur',
                    'data' => $dawisDataDesa->pluck('jumlah_sumur'),
                    'backgroundColor' => '#e67e22', // Orange
                    'hoverBackgroundColor' => '#d55d20',
                    'borderColor' => '#e67e22',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarSumur'
                ],
                [
                    'label' => 'Sumber Air Lain',
                    'data' => $dawisDataDesa->pluck('jumlah_sumber_air_lain'),
                    'backgroundColor' => '#2ecc71', // Green
                    'hoverBackgroundColor' => '#27ae60',
                    'borderColor' => '#2ecc71',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarSumberAirLain'
                ],
                [
                    'label' => 'Beras',
                    'data' => $dawisDataDesa->pluck('jumlah_makanan_pokok'),
                    'backgroundColor' => '#36b9cc',
                    'hoverBackgroundColor' => '#2c9faf',
                    'borderColor' => '#36b9cc',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarMakananPokok' // Dynamic chart ID for Makanan Pokok
                ],
                [
                    'label' => 'Non Beras',
                    'data' => $dawisDataDesa->pluck('jumlah_makanan_pokok_lain'),
                    'backgroundColor' => '#36b9cc',
                    'hoverBackgroundColor' => '#2c9faf',
                    'borderColor' => '#36b9cc',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarMakananPokokLain' // Dynamic chart ID for Makanan Pokok
                ],
                [
                    'label' => 'Aktivitas UP2K',
                    'data' => $dawisDataDesa->pluck('total_aktivitas_up2k'),
                    'backgroundColor' => '#f6c23e',
                    'hoverBackgroundColor' => '#e0a800',
                    'borderColor' => '#f6c23e',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarAktivitasUP2K' // Dynamic chart ID for Aktivitas UP2K
                ],
                [
                    'label' => 'Aktivitas Usaha Kesehatan Lingkungan',
                    'data' => $dawisDataDesa->pluck('total_aktivitas_usaha_kesehatan_lingkungan'),
                    'backgroundColor' => '#e74a3b',
                    'hoverBackgroundColor' => '#d23f31',
                    'borderColor' => '#e74a3b',
                    'borderWidth' => 1,
                    'chartId' => 'chartBarAktivitasUsahaKesehatanLingkungan' // Dynamic chart ID for Aktivitas Usaha Kesehatan Lingkungan
                ]
            ],
        ];


        // Return ke view laporan
        return view('user.dasawisma.laporan.statistikdesa', [
            'dawisData'=>$dawisData,
            'totals' =>$totals,
            'chartData'=> $chartData,
            'chartDataDawisPerItem' => $chartDataDawisPerItem,
        ]);

    }

    public function downloadPdf()
    {

        // Ambil user yang sedang login
        $user = auth()->user();

        // Pastikan user memiliki akses wilayah
        if (!$user->no_kel || !$user->no_kec || !$user->no_kab || !$user->no_prop) {
            return redirect()->back()->with('error', 'Akun Anda tidak memiliki akses ke data wilayah tertentu.');
        }

        // Query untuk mengambil data sesuai dengan wilayah user
        $dawisData = DB::table('data_keluarga AS dk')
            ->join('data_keluarga_akumulasi AS dka', 'dk.no_kk', '=', 'dka.no_kk')
            ->join('dawis AS dw', 'dk.dawis_id', '=', 'dw.id')
            ->select(
                'dw.id AS dawis_id',
                'dw.nama_dawis',
                'dk.no_kel',
                'dk.no_kec',
                'dk.no_kab',
                'dk.no_prop',
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
            ->where('dk.no_prop', $user->no_prop)
            ->where('dk.no_kab', $user->no_kab)
            ->where('dk.no_kec', $user->no_kec)
            ->where('dk.no_kel', $user->no_kel)
            ->groupBy('dw.id', 'dw.nama_dawis', 'dk.no_kel', 'dk.no_kec', 'dk.no_kab', 'dk.no_prop')
            ->get();
        //dd($dawisData);
        
            // Calculate totals
        $totals = [
            'jumlah_kepala_keluarga' => $dawisData->sum('jumlah_kepala_keluarga'),
            'total_jumlah_anggota_keluarga' => $dawisData->sum('total_jumlah_anggota_keluarga'),
            'total_balita' => $dawisData->sum('total_balita'),
            'total_pus' => $dawisData->sum('total_pus'),
            'total_wus' => $dawisData->sum('total_wus'),
            'total_ibu_hamil' => $dawisData->sum('total_ibu_hamil'),
            'total_ibu_menyusui' => $dawisData->sum('total_ibu_menyusui'),
            'total_lansia' => $dawisData->sum('total_lansia'),
            'total_buta_baca' => $dawisData->sum('total_buta_baca'),
            'total_buta_tulis' => $dawisData->sum('total_buta_tulis'),
            'total_buta_hitung' => $dawisData->sum('total_buta_hitung'),
            'total_difabel' => $dawisData->sum('total_difabel'),
            'jumlah_layak_huni' => $dawisData->sum('jumlah_layak_huni'),
            'jumlah_tidak_layak_huni' => $dawisData->sum('jumlah_tidak_layak_huni'),
            'total_tempat_sampah_keluarga' => $dawisData->sum('total_tempat_sampah_keluarga'),
            'total_saluran_air_limbah' => $dawisData->sum('total_saluran_air_limbah'),
            'total_jamban_keluarga' => $dawisData->sum('total_jamban_keluarga'),
            'total_jamban_keluarga_jumlah' => $dawisData->sum('total_jamban_keluarga_jumlah'),
            'total_stiker_p4k' => $dawisData->sum('total_stiker_p4k'),
            'jumlah_pdam' => $dawisData->sum('jumlah_pdam'),
            'jumlah_sumur' => $dawisData->sum('jumlah_sumur'),
            'jumlah_sumber_air_lain' => $dawisData->sum('jumlah_sumber_air_lain'),
            'jumlah_makanan_pokok' => $dawisData->sum('jumlah_makanan_pokok'),
            'jumlah_makanan_pokok_lain' => $dawisData->sum('jumlah_makanan_pokok_lain'),
            'total_aktivitas_up2k' => $dawisData->sum('total_aktivitas_up2k'),
            'jumlah_aktivitas_up2k_lain' => $dawisData->sum('jumlah_aktivitas_up2k_lain'),
            'total_memiliki_tabungan' => $dawisData->sum('total_memiliki_tabungan'),
            'total_aktivitas_usaha_kesehatan_lingkungan' => $dawisData->sum('total_aktivitas_usaha_kesehatan_lingkungan'),
        ];

        $pdf = PDF::loadView('user.dasawisma.laporan.laporanDawisPDF', [
            'dawisData' => $dawisData,
            'totals' => $totals
        ])
        ->setPaper('A4', 'landscape'); // Set paper orientation to landscape

        //dd($pdf);

    return $pdf->download('Laporan_Desa_PerDawis.pdf');


    }    

}
