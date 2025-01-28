@extends('admin.admin')

@section('main-content')


<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
            <div class="card-body" style="border-radius: 15px;">
                <div class="card-header">
                    <h5 class="font-weight-bold text-primary">DATA DASAWISMA</h5>
                </div>

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                @if (session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    {{ session('warning') }}
                    <button type="button" class="close" data-bs-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif

                <!-- Form pencarian -->
                <form action="{{ route('admin.dasawisma.index') }}" method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Cari Dasa Wisma..." value="{{ request()->get('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
                
                <!-- Cek jika ada data secara keseluruhan -->
                @if ($dasawisma->isEmpty() && !request()->has('search'))
                <div class="alert alert-warning text-center">
                    Tidak ada data Dasa Wisma. <a href="{{ route('admin.dasawisma.create') }}" class="alert-link">Buat sekarang</a>.
                </div>
                @endif
                <!-- Tombol "Tambah Dasa Wisma Baru" hanya muncul jika tidak ada pencarian -->
                @if (!request()->has('search'))

                <div class="d-flex justify-content-start mt-4">
                    <a href="{{ route('admin.dasawisma.create') }}" class="btn btn-success mr-3">
                        <i class="fas fa-plus"></i>
                        Tambah Dasa Wisma Baru</a>
                    <a href="#" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
                        <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                    </a>
                </div>
                @endif

                <br>
                <!-- Cek jika tidak ada hasil pencarian -->
                <!-- Cek jika tidak ada hasil pencarian -->
                @if ($dasawisma->isEmpty() && request()->has('search'))
                <div class="alert alert-warning">
                    Tidak ada data yang sesuai dengan pencarian atau filter yang dipilih.
                </div>
                <!-- Tambahkan tombol kembali jika pencarian tidak menghasilkan data -->
                <div class="mt-3">
                    <a href="{{ route('admin.dasawisma.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
                @endif

                
                <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                    @if ($dasawisma->isNotEmpty())
                    <table class="table table-bordered table-striped table-hover shadow" style="border-radius: 15px;">
                        <thead>
                            <tr>
                                <th rowspan="2" class="text-center align-middle">No</th>
                                <th rowspan="2" class="text-center align-middle">Nama Dasa Wisma</th>
                                <th rowspan="2" class="text-center align-middle">RT</th>
                                <th rowspan="2" class="text-center align-middle">RW</th>
                                <th rowspan="2" class="text-center align-middle">Dusun</th>
                                <th rowspan="2" class="text-center align-middle">Tahun</th>
                                <th rowspan="2" class="text-center align-middle">Provinsi</th>
                                <th rowspan="2" class="text-center align-middle">Kabupaten</th>
                                <th rowspan="2" class="text-center align-middle">Kecamatan</th>
                                <th rowspan="2" class="text-center align-middle">Kelurahan</th>
                                <th rowspan="2" class="text-center align-middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dasawisma as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->nama_dawis }}</td>
                                <td>{{ $item->rt }}</td>
                                <td>{{ $item->rw }}</td>
                                <td>{{ $item->dusun }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td>{{ $item->nama_prop }}</td>
                                <td>{{ $item->nama_kab }}</td>
                                <td>{{ $item->nama_kec }}</td>
                                <td>{{ $item->nama_kel }}</td>
            
                                <td>
                                    <div class="btn-group" style="display: flex; justify-content: center; align-items: center; gap: 5px;">
                                        <a href="{{ route('admin.dasawisma.kepalaRumahTangga', $item->id) }}" class="btn btn-primary btn-sm">KRT</a>
                                        <a href="{{ route('admin.dasawisma.show', $item->id) }}" class="btn btn-info btn-sm">Lihat</a>
                                        <a href="{{ route('admin.dasawisma.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.dasawisma.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus Dasa Wisma ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Tampilkan links pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $dasawisma->links() }}
                    </div>


                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        margin: 0;
        padding: 0;
    }

    .container {
        max-height: 100vh; /* Menyesuaikan dengan tinggi layar */
        overflow: hidden; /* Menghilangkan scroll */
        padding: 10px;
        box-sizing: border-box;
    }

    h3 {
        text-align: center;
        font-size: 18px;
        margin: 10px 0;
    }

    .buttons {
        display: flex;
        justify-content: center;
        margin-bottom: 10px;
        gap: 10px; /* Jarak antar tombol */
    }

    .buttons .btn {
        padding: 5px 10px;
        font-size: 12px;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .btn-success {
        background-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-primary {
        background-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        text-align: center;
    }

    table th, table td {
        border: 1px solid #ddd;
        padding: 3px;
    }

    table th {
        background-color: #007bff;
        color: white;
    }

    table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    table tbody tr:hover {
        background-color: #e2e6ea;
    }
</style>



<!-- Bootstrap 5 JavaScript CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-mQ93FUTfN7uZeWkh5f6vPuqG8tj1wC1rPZQx40L+qOFRp1lH3FIBczuWwZ5yJvN" crossorigin="anonymous"></script>
<!-- Popper.js (required for Bootstrap) -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
<!--
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
-->



@endsection