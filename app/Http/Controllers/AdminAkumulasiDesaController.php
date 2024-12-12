<?php

namespace App\Http\Controllers;

use App\Models\Kel;
use App\Models\Kec;  // Model desa yang digunakan
use App\Models\DataKeluarga;
use App\Models\DataKeluargaAkumulasi;
use Illuminate\Http\Request;

class AdminAkumulasiDesaController extends Controller
{
    public function index($no_prop, $no_kab, $no_kec, $no_kel = null)
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
    
        // Ambil data kelurahan berdasarkan $no_kel, $no_kec, $no_kab, dan $no_prop
        $kelurahan = Kel::where('no_kel', $no_kel)
                        ->where('no_kec', $no_kec)
                        ->where('no_kab', $no_kab)
                        ->where('no_prop', $no_prop)
                        ->first();
    
        // Pastikan kelurahan ditemukan dan tidak duplikat
        if (!$kelurahan) {
            return redirect()->back()->with('error', 'Kelurahan tidak ditemukan.');
        }
    
        // Ambil no_kk dari data_keluarga yang sesuai dengan no_kel
        $no_kk_list = DataKeluarga::where('no_kel', $no_kel)
                                    ->where('no_kec', $no_kec)
                                    ->where('no_kab', $no_kab)
                                    ->where('no_prop', $no_prop)
                                    ->pluck('no_kk');
    
        // Ambil data_keluarga_akumulasi berdasarkan no_kk
        $dataAkumulasi = DataKeluargaAkumulasi::with('dataKeluarga')
            ->whereIn('no_kk', $no_kk_list)
            ->get();
    
        // Kirimkan data ke tampilan
        return view('admin.laporan.desa.akumulasidesa.index', [
            'kecamatanId' => $kecamatan ? $kecamatan->nama_kec : 'Kecamatan tidak ditemukan',
            'kelurahanId' => $kelurahan ? $kelurahan->nama_kelurahan : 'Kelurahan tidak ditemukan',
            'desaId' => $kelurahan ? $kelurahan->nama_kel : 'Nama Desa tidak ditemukan', // Mengambil nama_kelurahan
            'desaList' => $desaList,
            'dataAkumulasi' => $dataAkumulasi,
            'no_prop' => $no_prop,
            'no_kab' => $no_kab,
            'no_kec' => $no_kec,
        ]);
    }
    

}
