<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;
use App\Models\Message;

use App\Models\Prop;
use App\Models\Kab;
use App\Models\Kec;
use App\Models\Kel;

class SuperAdminController extends Controller
{
    // Menampilkan daftar administrator
    public function index()
    {
        // Mengambil semua user yang memiliki role 'Administrator'
        $admins = User::role('Administrator')->get();

        return view('superadmin.admins.index', compact('admins'));
    }

    // Menampilkan form untuk menambahkan admin baru
    public function create()
    {
        $provinsi = Prop::all();
        $kabupaten = [];
        $kecamatan = [];
        $kelurahan = [];

        return view('superadmin.admins.create', compact('provinsi', 'kabupaten', 'kecamatan', 'kelurahan'));
        //return view('superadmin.admins.create');
    }

    // Menyimpan admin baru
    public function store(Request $request)
    {
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
        // Mendefinisikan variabel $data
        $data = $request->only(['name', 'last_name', 'email', 'password', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan']); // Mengambil input yang dibutuhkan

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


    // Menampilkan form edit admin
    public function edit($id)
    {
        $admin = User::findOrFail($id);
        return view('superadmin.admins.edit', compact('admin'));
    }

    // Mengupdate data admin
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $admin->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $admin->password,
        ]);

        return redirect()->route('superadmin.admins.index')->with('success', 'Administrator berhasil diupdate.');
    }

    // Menghapus admin
    public function destroy($id)
    {
        $admin = User::findOrFail($id);
        $admin->delete();

        return redirect()->route('superadmin.admins.index')->with('success', 'Administrator berhasil dihapus.');
    }
    
    // Menampilkan daftar pengguna
    public function userIndex()
    {
        // Mengambil semua user yang tidak memiliki role 'Administrator' atau 'superadmin'
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Administrator', 'superadmin']);
        })
        ->leftJoin('kel', function ($join) {
            $join->on('users.no_prop', '=', 'kel.no_prop')
                ->on('users.no_kab', '=', 'kel.no_kab')
                ->on('users.no_kec', '=', 'kel.no_kec')
                ->on('users.no_kel', '=', 'kel.no_kel');
        })
        ->leftJoin('kec', function ($join) {
            $join->on('kel.no_prop', '=', 'kec.no_prop')
                ->on('kel.no_kab', '=', 'kec.no_kab')
                ->on('kel.no_kec', '=', 'kec.no_kec');
        })
        ->leftJoin('kab', function ($join) {
            $join->on('kec.no_prop', '=', 'kab.no_prop')
                ->on('kec.no_kab', '=', 'kab.no_kab');
        })
        ->leftJoin('prop', 'users.no_prop', '=', 'prop.no_prop') // Menambahkan join untuk propinsi
        ->select('users.*', 'prop.nama_prop', 'kab.nama_kab', 'kec.nama_kec', 'kel.nama_kel')
        ->paginate(10);

        return view('superadmin.users.index', compact('users'));
    }


    // Menampilkan form untuk menambahkan pengguna baru
    public function userCreate()
    {
        $provinsi = Prop::all();
        $kabupaten = [];
        $kecamatan = [];
        $kelurahan = [];
        

        return view('superadmin.users.create', compact('provinsi', 'kabupaten', 'kecamatan', 'kelurahan'));
    }

    // Menyimpan pengguna baru
    public function userStore(Request $request)
    {
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

        // Mendefinisikan variabel $data
        $data = $request->only(['name', 'last_name', 'email', 'password', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan']); // Mengambil input yang dibutuhkan

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

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function userEdit($id)
    {
        // Ambil data user beserta nama provinsi, kabupaten, kecamatan, dan kelurahan
        $user = User::select(
            'users.*',
            'prop.nama_prop',
            'kab.nama_kab',
            'kec.nama_kec',
            'kel.nama_kel'
        )
        ->leftJoin('prop', 'users.no_prop', '=', 'prop.no_prop')
        ->leftJoin('kab', 'users.no_kab', '=', 'kab.no_kab')
        ->leftJoin('kec', 'users.no_kec', '=', 'kec.no_kec')
        ->leftJoin('kel', 'users.no_kel', '=', 'kel.no_kel')
        ->where('users.id', $id)
        ->firstOrFail();

        // Ambil data untuk dropdown
        $provinsi = Prop::all();
        $kabupaten = Kab::where('no_prop', $user->no_prop)->get();
        $kecamatan = Kec::where('no_kab', $user->no_kab)->get();
        $kelurahan = Kel::where('no_kec', $user->no_kec)->get();

        return view('superadmin.users.edit', compact('user', 'provinsi', 'kabupaten', 'kecamatan', 'kelurahan'));
    }

    public function userUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'provinsi' => 'required|integer',
            'kabupaten' => 'required|integer',
            'kecamatan' => 'required|integer',
            'kelurahan' => 'required|integer',
        ]);

        // Update data pengguna
        $user->update([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'no_prop' => $request->provinsi,
            'no_kab' => $request->kabupaten,
            'no_kec' => $request->kecamatan,
            'no_kel' => $request->kelurahan,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil diupdate.');
    }

    // Menghapus pengguna
    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function about()
    {
        return view('superadmin.about');
    }
    public function getNotifications()
    {
        $notifications = Notification::orderBy('created_at', 'desc')->limit(5)->get();
        return view('superadmin.notifications', compact('notifications'));
    }
    public function getMessages()
    {
        $messages = Message::orderBy('created_at', 'desc')->limit(5)->get();
        return view('superadmin.messages', compact('messages'));
    }
    public function sendMessage(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Simpan pesan ke dalam database
        Message::create($validatedData);

        // Buat notifikasi baru
        Notification::create([
            'title' => 'Pesan Baru Diterima',
            'description' => 'Anda telah menerima pesan baru dari ' . $request->name,
        ]);

        // Redirect dengan pesan sukses
        return back()->with('success', 'Pesan Anda telah dikirim!');
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
