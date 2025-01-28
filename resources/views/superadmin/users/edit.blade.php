@extends('superadmin.admin')

@section('main-content')
<h1>Edit Pengguna</h1>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('superadmin.users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="name">Nama Depan</label>
        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
    </div>

    <div class="form-group">
        <label for="last_name">Nama Belakang</label>
        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
    </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
    </div>

    <div class="form-group">
        <label for="provinsi">Provinsi</label>
        <select id="provinsi" name="provinsi" class="form-control" required>
            @foreach($provinsi as $p)
            <option value="{{ $p->no_prop }}" {{ $p->no_prop == $user->no_prop ? 'selected' : '' }}>{{ $p->nama_prop }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="kabupaten">Kabupaten</label>
        <select id="kabupaten" name="kabupaten" class="form-control" required>
            @foreach($kabupaten as $kab)
            <option value="{{ $kab->no_kab }}" {{ $kab->no_kab == $user->no_kab ? 'selected' : '' }}>{{ $kab->nama_kab }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="kecamatan">Kecamatan</label>
        <select id="kecamatan" name="kecamatan" class="form-control" required>
            @foreach($kecamatan as $kec)
            <option value="{{ $kec->no_kec }}" {{ $kec->no_kec == $user->no_kec ? 'selected' : '' }}>{{ $kec->nama_kec }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="kelurahan">Kelurahan</label>
        <select id="kelurahan" name="kelurahan" class="form-control" required>
            @foreach($kelurahan as $kel)
            <option value="{{ $kel->no_kel }}" {{ $kel->no_kel == $user->no_kel ? 'selected' : '' }}>{{ $kel->nama_kel }}</option>
            @endforeach
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
</form>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Ambil kabupaten berdasarkan provinsi yang dipilih
        $('#provinsi').on('change', function() {
            var provinsiID = $(this).val();
            $('#kabupaten').empty().append('<option value="">-- Pilih Kabupaten --</option>');
            $('#kecamatan').empty().append('<option value="">-- Pilih Kecamatan --</option>');
            $('#kelurahan').empty().append('<option value="">-- Pilih Kelurahan --</option>');

            if (provinsiID) {
                $.ajax({
                    url: '/api/kabupaten/' + provinsiID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('#kabupaten').append('<option value="' + value.no_kab + '">' + value.nama_kab + '</option>');
                        });
                    },
                    error: function() {
                        alert('Error retrieving kabupaten data. Please try again.');
                    }
                });
            }
        });

        // Ambil kecamatan berdasarkan kabupaten yang dipilih
        $('#kabupaten').on('change', function() {
            var kabupatenID = $(this).val();
            $('#kecamatan').empty().append('<option value="">-- Pilih Kecamatan --</option>');
            $('#kelurahan').empty().append('<option value="">-- Pilih Kelurahan --</option>');

            if (kabupatenID) {
                $.ajax({
                    url: '/api/kecamatan/' + kabupatenID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('#kecamatan').append('<option value="' + value.no_kec + '">' + value.nama_kec + '</option>');
                        });
                    },
                    error: function() {
                        alert('Error retrieving kecamatan data. Please try again.');
                    }
                });
            }
        });

        // Ambil kelurahan berdasarkan kecamatan yang dipilih
        $('#kecamatan').on('change', function() {
            var kecamatanID = $(this).val();
            $('#kelurahan').empty().append('<option value="">-- Pilih Kelurahan --</option>');

            if (kecamatanID) {
                $.ajax({
                    url: '/api/kelurahan/' + kecamatanID,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $.each(data, function(key, value) {
                            // Tampilkan nama kelurahan sesuai kecamatan
                            $('#kelurahan').append('<option value="' + value.no_kel + '">' + value.nama_kel + '</option>');
                        });
                    },
                    error: function() {
                        alert('Error retrieving kelurahan data. Please try again.');
                    }
                });
            }
        });
    });
</script>
@endsection