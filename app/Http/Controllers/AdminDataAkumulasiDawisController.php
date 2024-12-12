<?php

namespace App\Http\Controllers;


use App\Models\Kel;
use App\Models\Dawis;  // Model desa yang digunakan
use App\Models\DataKeluarga;
use App\Models\DataKeluargaAkumulasi;

class AdminDataAkumulasiDawisController extends Controller
{
    public function index($no_prop, $no_kab, $no_kec, $no_kel, $dawis_id)
    {
        // Ambil data Dawis tertentu beserta data keluarga dan data keluarga akumulasi yang terkait
        $dawis = Dawis::where('id', $dawis_id)
            ->where('no_prop', $no_prop)
            ->where('no_kab', $no_kab)
            ->where('no_kec', $no_kec)
            ->where('no_kel', $no_kel)
            ->with(['dataKeluarga.dataKeluargaAkumulasi'])
            ->first();

        // Pastikan data ditemukan
        if (!$dawis) {
            return redirect()->back()->with('error', 'Data Dawis tidak ditemukan.');
        }

        $dataAkumulasi = $dawis->dataKeluarga->map->akumulasi; // Mengumpulkan data akumulasi dari data keluarga
        $nama_dawis = $dawis->nama_dawis; // Menyimpan nama Dawis

        return view('admin.laporan.desa.dawisdesa.akumulasidawis', compact('dawis', 'dataAkumulasi', 'nama_dawis'));
    }

    
}
