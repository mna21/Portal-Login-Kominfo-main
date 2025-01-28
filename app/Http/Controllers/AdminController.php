<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Models\Prop;
use App\Models\Kab;
use App\Models\Kec;
use App\Models\Kel;
use App\Models\Dawis; // Import model Dawis
use App\Models\KepalaRumahTangga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Menampilkan daftar administrator

    // Menampilkan daftar pengguna
    public function Index()
    {
        // Mengambil semua user yang tidak memiliki role 'Administrator' atau 'superadmin'
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Administrator', 'superadmin']);
        })->get();

        return view('admin.users.index', compact('users'));
    }

    // Menampilkan form untuk menambahkan pengguna baru
    public function userCreate()
    {
        $provinsi = Prop::all();
        $kabupaten = [];
        $kecamatan = [];
        $kelurahan = [];
        

        return view('admin.users.create', compact('provinsi', 'kabupaten', 'kecamatan', 'kelurahan'));
    }

    //PENGGUNA BARU
    public function userStore(Request $request)
    {
        // Validasi input yang diterima dari form
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'provinsi' => 'required|integer',
            'kabupaten' => 'required|integer',
            'kecamatan' => 'required|integer',
            'kelurahan' => 'required|integer',
        ]);

        // Mendefinisikan variabel $data untuk mengambil input yang dibutuhkan
        // Pastikan menggunakan nama yang sesuai dengan yang ada di form
        $data = $request->only(['name', 'last_name', 'email', 'password', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan']);

        // Membuat pengguna baru dengan data yang telah disaring
        $user = User::create([
            'name' => $data['name'],  // Menggunakan $data untuk mengakses nama
            'last_name' => $data['last_name'],  // Menggunakan $data untuk mengakses last_name
            'email' => $data['email'],  // Menggunakan $data untuk mengakses email
            'password' =>($data['password']),  // Menggunakan $data untuk password dan mengenkripsi
            'role' => 'user', // Default role
            'no_prop' => $data['provinsi'],  // Menggunakan $data untuk no_prop (provinsi)
            'no_kab' => $data['kabupaten'],  // Menggunakan $data untuk no_kab (kabupaten)
            'no_kec' => $data['kecamatan'],  // Menggunakan $data untuk no_kec (kecamatan)
            'no_kel' => $data['kelurahan'],  // Menggunakan $data untuk no_kel (kelurahan)
        ]);

        Log::info('User created with ID: ' . $user->id);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }



    // Menampilkan form edit pengguna
    public function userEdit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // Mengupdate data pengguna
    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diupdate.');
    }

    // Menghapus pengguna
    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }


    
    public function getKabupaten($provinsi)
    {
        $kabupaten = Kab::where('no_prop', $provinsi)->get();
        return response()->json(['status' => 'success', 'data' => $kabupaten]);
    }

    public function getKecamatan($kabupaten)
    {
        $kecamatan = Kec::where('no_kab', $kabupaten)->get();
        return response()->json(['status' => 'success', 'data' => $kecamatan]);
    }

    public function getKelurahan($kecamatan)
    {
        $kelurahan = Kel::where('no_kec', $kecamatan)->get();
        return response()->json(['status' => 'success', 'data' => $kelurahan]);
    }



    public function provinsi()
    {
        return $this->belongsTo(Prop::class, 'no_prop', 'no_prop');
    }
    
    public function kabupaten()
    {
        return $this->belongsTo(Kab::class, 'no_kab', 'no_kab');
    }
    
    public function kecamatan()
    {
        return $this->belongsTo(Kec::class, 'no_kec', 'no_kec');
    }
    
    public function kelurahan()
    {
        return $this->belongsTo(Kel::class, 'no_kel', 'no_kel');
    }
    
}
