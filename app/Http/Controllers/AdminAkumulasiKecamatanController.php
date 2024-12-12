<?php

namespace App\Http\Controllers;

use App\Models\Kel;
use App\Models\Kec;
use App\Models\DataKeluarga;
use App\Models\DataKeluargaAkumulasi;
use Illuminate\Http\Request;

class AdminAkumulasiKecamatanController extends Controller
{
    public function index($no_kec, $no_kab, $no_prop)
    {
        // Ambil data desa berdasarkan $no_kec, $no_kab, dan $no_prop
        $desaList = Kel::where('no_kec', $no_kec)
                    ->where('no_kab', $no_kab)
                    ->where('no_prop', $no_prop)
                    ->get();

        // Ambil data kecamatan berdasarkan $no_kec
        $kecamatan = Kec::where('no_kec', $no_kec)
                        ->where('no_kab', $no_kab)
                        ->where('no_prop', $no_prop)
                        ->first();

        // Ambil no_kk dari data_keluarga yang sesuai dengan no_kec
        $no_kk_list = DataKeluarga::where('no_kec', $no_kec)->pluck('no_kk');

        // Ambil data_keluarga_akumulasi berdasarkan no_kk
        //$dataAkumulasi = DataKeluargaAkumulasi::whereIn('no_kk', $no_kk_list)->get();

        $dataAkumulasi = DataKeluargaAkumulasi::with('dataKeluarga')
        ->whereHas('dataKeluarga', function($query) use ($no_kec) {
            $query->where('no_kec', $no_kec);
        })->get();
        //dd($dataAkumulasi);


        // Kirimkan data ke tampilan
        return view('admin.laporan.akumulasikecamatan.index', [
            'kecamatanId' => $kecamatan ? $kecamatan->nama_kec : 'Kecamatan tidak ditemukan',
            'desaList' => $desaList,
            'dataAkumulasi' => $dataAkumulasi, // Data akumulasi keluarga di kecamatan
        ]);
    }

}