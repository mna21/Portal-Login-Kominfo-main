@extends('admin.admin')

@section('main-content')

<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
            <div class="card-body" style="border-radius: 15px;">
                <!-- Header -->
                <div class="card-header">
                    <h5 class="font-weight-bold text-primary">DAFTAR USERS</h5>
                </div>

                <!-- Tombol Tambah Pengguna -->
                <div class="mt-3">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-user-plus"></i> Tambah Pengguna
                    </a>
                </div>

                <!-- Notifikasi -->
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <!-- Tabel Daftar Users -->
                <div class="table-responsive mt-3" style="overflow-x: auto; overflow-y: hidden;">
                    <table class="table table-bordered table-striped table-hover shadow" style="border-radius: 15px;">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>Nama Depan</th>
                                <th>Nama Belakang</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <div class="btn-group" style=" justify-content: center; align-items: center; gap: 5px;">
                                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda yakin ingin menghapus pengguna ini?')">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
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
