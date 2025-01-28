@extends('superadmin.admin')

@section('main-content')
<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
            <div class="card-body" style="border-radius: 15px;">
                <div class="card-header">
                    <h5 class="font-weight-bold text-primary">Daftar Users</h5>
                </div>

                <div class="mt-3">
                    <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-user-plus"></i> Tambah Users
                    </a>
                </div>

                 <!-- Notifikasi -->
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                

                <div class="table-responsive mt-3" style="overflow-x: auto; overflow-y: hidden;">
                    <table class="table table-bordered table-striped table-hover shadow" style="border-radius: 15px;">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Nama Depan</th>
                                <th>Nama Belakang</th>
                                <th>Email</th>
                                <th>Provinsi</th>
                                <th>Kabupaten</th>
                                <th>Kecamatan</th>
                                <th>Kelurahan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->provinsi ? $user->provinsi->nama_prop : 'Tidak tersedia' }}</td>
                                <td>{{ $user->kabupaten ? $user->kabupaten->nama_kab : 'Tidak tersedia' }}</td>
                                <td>{{ $user->kecamatan ? $user->kecamatan->nama_kec : 'Tidak tersedia' }}</td>
                                <td>{{ $user->kelurahan ? $user->kelurahan->nama_kel : 'Tidak tersedia' }}</td>
                                <td>
                                    <a href="{{ route('superadmin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                    <form action="{{ route('superadmin.users.destroy', $user->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection