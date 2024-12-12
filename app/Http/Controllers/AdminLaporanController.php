<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Kec;  // Pastikan ada model Kecamatan
use App\Models\Kabupaten;
use App\Models\Prop;

class AdminLaporanController extends Controller
{
    public function index()
    {
        // Ambil semua data kecamatan
        $kecamatanList = Kec::all();

        // Debug hasil query
        //dd($kecamatanList);

        // Kembalikan view dengan data kecamatan
        return view('admin.laporan.kecamatan.index', compact('kecamatanList'));
    }
}


