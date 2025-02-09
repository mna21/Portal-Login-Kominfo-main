@extends('user.admin')

@section('main-content')

<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
            <div class="card-body" style="border-radius: 15px;">

                <div class="card-header">
                    <h6 class="font-weight-bold text-primary">Edit Data Dasawisma</h6>
                </div>

                <form action="{{ route('user.dasawisma.update', $dasaWisma->id) }}" method="POST">
                    @csrf
                    @method('PUT')
            
                    <div class="form-group">
                        <label for="nama_dawis">Nama Dasa Wisma</label>
                        <input type="text" name="nama_dawis" id="nama_dawis" class="form-control" value="{{ old('nama_dawis', $dasaWisma->nama_dawis) }}" required>
                    </div>
            
                    <div class="form-group">
                        <label for="provinsi">Provinsi</label>
                        <select name="provinsi" id="provinsi" class="form-control" required>
                            <option value="">Pilih Provinsi</option>
                            @foreach ($provinsi as $p)
                            <option value="{{ $p->no_prop }}" {{ old('provinsi', $dasaWisma->no_prop) == $p->no_prop ? 'selected' : '' }}>{{ $p->nama_prop }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="form-group">
                        <label for="kabupaten">Kabupaten</label>
                        <select name="kabupaten" id="kabupaten" class="form-control" required>
                            <option value="">Pilih Kabupaten</option>
                            @foreach ($kabupaten as $k)
                            <option value="{{ $k->no_kab }}" {{ old('kabupaten', $dasaWisma->no_kab) == $k->no_kab ? 'selected' : '' }}>{{ $k->nama_kab }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="form-group">
                        <label for="kecamatan">Kecamatan</label>
                        <select name="kecamatan" id="kecamatan" class="form-control" required>
                            <option value="">Pilih Kecamatan</option>
                            @foreach ($kecamatan as $kc)
                            <option value="{{ $kc->no_kec }}" {{ old('kecamatan', $dasaWisma->no_kec) == $kc->no_kec ? 'selected' : '' }}>{{ $kc->nama_kec }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="form-group">
                        <label for="kelurahan">Kelurahan</label>
                        <select name="kelurahan" id="kelurahan" class="form-control" required>
                            <option value="">Pilih Kelurahan</option>
                            @foreach ($kelurahan as $kl)
                            <option value="{{ $kl->no_kel }}-{{ $kl->no_kec }}-{{ $kl->no_kab }}-{{ $kl->no_prop }}" {{ old('kelurahan', $dasaWisma->no_kel . '-' . $dasaWisma->no_kec . '-' . $dasaWisma->no_kab . '-' . $dasaWisma->no_prop) == ($kl->no_kel . '-' . $kl->no_kec . '-' . $kl->no_kab . '-' . $kl->no_prop) ? 'selected' : '' }}>{{ $kl->nama_kel }}</option>
                            @endforeach
                        </select>
                    </div>
            
                    <div class="form-group">
                        <label for="rt">RT</label>
                        <input type="number" name="rt" id="rt" class="form-control" value="{{ old('rt', $dasaWisma->rt) }}" required>
                    </div>
            
                    <div class="form-group">
                        <label for="rw">RW</label>
                        <input type="number" name="rw" id="rw" class="form-control" value="{{ old('rw', $dasaWisma->rw) }}" required>
                    </div>
            
                    <div class="form-group">
                        <label for="dusun">Dusun</label>
                        <input type="text" name="dusun" id="dusun" class="form-control" value="{{ old('dusun', $dasaWisma->dusun) }}">
                    </div>
            
                    <div class="form-group">
                        <label for="tahun">Tahun</label>
                        <input type="number" name="tahun" id="tahun" class="form-control" value="{{ old('tahun', $dasaWisma->tahun) }}" required>
                    </div>
            
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary d-inline-block">Perbarui</button>
                        <a href="{{ route('user.dasawisma.index') }}" class="btn btn-secondary d-inline-block ml-2">Kembali ke Daftar Dasa Wisma</a>
                    </div>
            
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
                            // Ubah value untuk menyimpan kombinasi no_kel, no_kec, no_kab, no_prop
                            $('#kelurahan').append('<option value="' + value.no_kel + '-' + value.no_kec + '-' + value.no_kab + '-' + value.no_prop + '">' + value.nama_kel + '</option>');
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