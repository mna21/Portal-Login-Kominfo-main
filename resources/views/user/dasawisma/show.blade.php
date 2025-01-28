@extends('user.admin')

@section('main-content')

<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
            <div class="card-body" style="border-radius: 15px;">
                <div class="card-header">
                    <h6 class="font-weight-bold text-primary">Detail: {{ $dawis->nama_dawis }}</h6>
                </div>

                <table class="table table-bordered table-striped table-hover shadow" style="border-radius: 15px;">
                    <thead>
                        <tr>
                            <th style="background-color: #007bff; color: white; text-align: left; padding: 8px;">Label</th>
                            <th style="background-color: #007bff; color: white; text-align: left; padding: 8px;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Nama Dasa Wisma</th>
                            <td>{{ $dawis->nama_dawis }}</td>
                        </tr>
                        <tr>
                            <th>RT</th>
                            <td>{{ $dawis->rt }}</td>
                        </tr>
                        <tr>
                            <th>RW</th>
                            <td>{{ $dawis->rw }}</td>
                        </tr>
                        <tr>
                            <th>Dusun</th>
                            <td>{{ $dawis->dusun }}</td>
                        </tr>
                        <tr>
                            <th>Provinsi</th>
                            <td>{{ $dawis->nama_prop }}</td>
                        </tr>
                        <tr>
                            <th>Kabupaten</th>
                            <td>{{ $dawis->nama_kab }}</td>
                        </tr>
                        <tr>
                            <th>Kecamatan</th>
                            <td>{{ $dawis->nama_kec }}</td>
                        </tr>
                        <tr>
                            <th>Kelurahan</th>
                            <td>{{ $dawis->nama_kel }}</td>
                        </tr>
                        <tr>
                            <th>Tahun</th>
                            <td>{{ $dawis->tahun }}</td>
                        </tr>
                    </tbody>
                </table>
            
                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('user.dasawisma.edit', $dawis->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('user.dasawisma.destroy', $dawis->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Anda yakin ingin menghapus Dasa Wisma ini?');">Hapus</button>
                    </form>
                </div>
            
                <div class="mt-2">
                    <a href="{{ route('user.dasawisma.index') }}" class="btn btn-secondary">Kembali ke Daftar Dasa Wisma</a>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection