@extends('admin.admin')

@section('main-content')

<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
            <div class="card-body" style="border-radius: 15px;">
                <div class="card-header">
                    <h5 class="font-weight-bold text-primary">Tambah Users</h5>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name">Nama Depan</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Nama Belakang</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="form-group">
                        <label for="provinsi">Provinsi</label>
                        <select name="provinsi" id="provinsi" class="form-control" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach ($provinsi as $p)
                            <option value="{{ $p->no_prop }}">{{ $p->nama_prop }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dropdown Kabupaten -->
                    <div class="form-group">
                        <label for="kabupaten">Kabupaten</label>
                        <select id="kabupaten" name="kabupaten" class="form-control">
                            <option value="">-- Pilih Kabupaten --</option>
                            <!-- Data kabupaten akan diisi melalui JavaScript -->
                        </select>
                    </div>

                    <!-- Dropdown Kecamatan -->
                    <div class="form-group">
                        <label for="kecamatan">Kecamatan</label>
                        <select id="kecamatan" name="kecamatan" class="form-control">
                            <option value="">-- Pilih Kecamatan --</option>
                            <!-- Data kecamatan akan diisi melalui JavaScript -->
                        </select>
                    </div>

                    <!-- Dropdown Kelurahan -->
                    <div class="form-group">
                        <label for="kelurahan">Kelurahan</label>
                        <select id="kelurahan" name="kelurahan" class="form-control">
                            <option value="">-- Pilih Kelurahan --</option>
                            <!-- Data kelurahan akan diisi melalui JavaScript -->
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
</div>

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

