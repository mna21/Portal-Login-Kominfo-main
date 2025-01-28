<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Prop;
use App\Models\Kab;
use App\Models\Kec;
use App\Models\Kel;
use App\Models\Dawis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    /**
     * Tampilkan dashboard untuk user.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil user yang sedang login
        $user = Auth::user();

        // Pastikan user memiliki no_kel dan no_kec
        if (!$user->no_kel || !$user->no_kec) {
            return redirect()->back()->with('error', 'Akun Anda tidak memiliki akses ke data wilayah tertentu.');
        }

        // Hitung jumlah Dawis berdasarkan no_kel dan no_kec user
        $totalDawis = DB::table('dawis')
            ->where('no_kel', $user->no_kel)
            ->where('no_kec', $user->no_kec)
            ->count();

        // Mengumpulkan data untuk widget
        $widget = [
            'total_dawis' => $totalDawis,
        ];

        // Mengembalikan view dengan data widget
        return view('user.dashboard', compact('widget'));
    }

    /**
     * Mendapatkan data kabupaten berdasarkan provinsi.
     *
     * @param string $provinsi
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKabupaten($provinsi)
    {
        $kabupaten = Kab::where('no_prop', $provinsi)->get();

        if ($kabupaten->isEmpty()) {
            return response()->json(['message' => 'Kabupaten tidak ditemukan'], 404);
        }

        return response()->json($kabupaten);
    }

    /**
     * Mendapatkan data kecamatan berdasarkan kabupaten.
     *
     * @param string $kabupaten
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKecamatan($kabupaten)
    {
        $kecamatan = Kec::where('no_kab', $kabupaten)->get();

        if ($kecamatan->isEmpty()) {
            return response()->json(['message' => 'Kecamatan tidak ditemukan'], 404);
        }

        return response()->json($kecamatan);
    }

    /**
     * Mendapatkan data kelurahan berdasarkan kecamatan.
     *
     * @param string $kecamatan
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKelurahan($kecamatan)
    {
        $kelurahan = Kel::where('no_kec', $kecamatan)->get();

        if ($kelurahan->isEmpty()) {
            return response()->json(['message' => 'Kelurahan tidak ditemukan'], 404);
        }

        return response()->json($kelurahan);
    }
}
