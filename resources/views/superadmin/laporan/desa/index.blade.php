
@extends('superadmin.admin')

@section('main-content')
<!---<div class="container">--->
    
    <div class="row">
        <div class="col-xl-12 mb-4">
            <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
                <div class="card-body" style="border-radius: 15px;">
                    <div class="card-header py-3">
                        <h5 class="m-0 font-weight-bold text-primary font-size: 1.5rem; ">AKUMULASI DASAWISMA : KECAMATAN {{$namaKec}}</h5>
                    </div>
                    
                    <div class="table-responsive" style="overflow-x: auto; overflow-y: hidden;">
                        <table class="table table-bordered table-striped">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th rowspan="2" class="text-center align-middle">No</th>
                                    <th rowspan="2" class="align-middle text-center">Desa</th>
                                    <th rowspan="2" class="align-middle text-center">JML KK</th>
                                    <!--<th rowspan="2" class="align-middle">Jumlah KK</th>-->
                                    <th colspan="11" class="highlight-yellow text-center align-middle">Jumlah Anggota Keluarga</th>
                                    <th colspan="7" class="highlight-orange text-center align-middle">Kriteria Rumah</th>
                                    <th colspan="3" class="highlight-green text-center align-middle">Sumber Air Keluarga</th>
                                    <th colspan="2" class="highlight-gray text-center align-middle">Makanan Pokok</th>
                                    <th colspan="4" class="align-middle text-center">Warga Mengikuti Kegiatan</th>
                                    <th rowspan="2" class="text-center align-middle">Aksi</th>
                                    <!--<th rowspan="2" class="text-center align-middle">Keterangan</th>  Menggunakan rowspan dan align center -->
                                </tr>
                                <tr>
                                    <!--Anggota Keluarga-->
                                    <th>Jumlah Anggota Keluarga</th>
                                    <th>Total Balita</th>
                                    <th>PUS</th>
                                    <th>WUS</th>
                                    <th>Ibu Hamil</th>
                                    <th>Ibu Menyusui</th>
                                    <th>Lansia</th>
                                    <th>Buta Baca</th>
                                    <th>Buta Tulis</th>
                                    <th>Buta Hitung</th>
                                    <th>Berkebutuhan Khusus</th>
                
                                    <!--Kriteria Rumah-->
                                    <th>Layak Huni</th>
                                    <th>Tidak Layak Huni</th>
                                    <th>Memiliki Tempat Pembuangan Sampah</th>
                                    <th>Saluran Limbah</th>
                                    <th>Memiliki Jamban Keluarga</th>
                                    <th>Jumlah Jamban Keluarga</th>
                                    <th>Memiliki Stiker P4K</th>
                
                
                                    <th>PDAM</th>
                                    <th>Sumur</th>
                                    <th>Sumber Air Keluarga Lain</th>
                
                                    <th>Beras</th>
                                    <th>Non Beras</th>
                
                                    <th>Akifitas UP2K</th>
                                    <th>Aktifitas UP2K Lain</th>
                                    <th>Tabungan</th>
                                    <th>Aktifitas Usaha Kesehatan Lingkungan</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataPerDesa as $index => $data)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $data->nama_kel }}</td>  <!-- Display nama_kec -->
                                        <td>{{ $data->jumlah_kepala_keluarga }}</td>
                                        <td>{{ $data->total_jumlah_anggota_keluarga }}</td>
                                        <td>{{ $data->total_balita }}</td>
                                        <td>{{ $data->total_pus }}</td>
                                        <td>{{ $data->total_wus }}</td>
                                        <td>{{ $data->total_ibu_hamil }}</td>
                                        <td>{{ $data->total_ibu_menyusui }}</td>
                                        <td>{{ $data->total_lansia }}</td>
                                        <td>{{ $data->total_buta_baca }}</td>
                                        <td>{{ $data->total_buta_tulis }}</td>
                                        <td>{{ $data->total_buta_hitung }}</td>
                                        <td>{{ $data->total_difabel}}</td>

                                        <td>{{ $data->jumlah_layak_huni }}</td>
                                        <td>{{ $data->jumlah_tidak_layak_huni }}</td>
                                        <td>{{ $data->total_tempat_sampah_keluarga }}</td>
                                        <td>{{ $data->total_saluran_air_limbah }}</td>
                                        <td>{{ $data->total_jamban_keluarga }}</td>
                                        <td>{{ $data->total_jamban_keluarga_jumlah }}</td>
                                        <td>{{ $data->total_stiker_p4k }}</td>

                                        <td>{{ $data->jumlah_pdam }}</td>
                                        <td>{{ $data->jumlah_sumur }}</td>
                                        <td>{{ $data->jumlah_sumber_air_lain }}</td>

                                        <td>{{ $data->jumlah_makanan_pokok }}</td>
                                        <td>{{ $data->jumlah_makanan_pokok_lain }}</td>

                                        <td>{{ $data->total_aktivitas_up2k }}</td>
                                        <td>{{ $data->jumlah_aktivitas_up2k_lain }}</td>
                                        <td>{{ $data->total_memiliki_tabungan }}</td>
                                        <td>{{ $data->total_aktivitas_usaha_kesehatan_lingkungan }}</td>

                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('superadmin.laporan.desa.dawisdesa.index', [
                                                    'no_prop' => $data->no_prop,
                                                    'no_kab' => $data->no_kab,
                                                    'no_kec' => $data->no_kec,
                                                    'no_kel' => $data->no_kel
                                                ]) }}" class="btn btn-primary btn-sm">Dawis</a>

                                                <a href="{{ route('superadmin.laporan.desa.dawisdesa.statistikdesa', [
                                                    'no_prop' => $data->no_prop,
                                                    'no_kab' => $data->no_kab,
                                                    'no_kec' => $data->no_kec,
                                                    'no_kel' => $data->no_kel
                                                ]) }}" class="btn btn-danger btn-sm"><i class="fas fa-fw fa-chart-bar"></i></a>
                    
                                                
                                            </div>
                                        </td>
                                        
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot class="bg-primary text-white">
                                <tr class="text-center align-middle">
                                    <th colspan="2">TOTAL</th>
                                    <th>{{ $totalKeseluruhan->jumlah_kepala_keluarga }}</th>
                                    <th>{{ $totalKeseluruhan->total_jumlah_anggota_keluarga }}</th>
                                    <th>{{ $totalKeseluruhan->total_balita }}</th>
                                    <th>{{ $totalKeseluruhan->total_pus }}</th>
                                    <th>{{ $totalKeseluruhan->total_wus }}</th>
                                    <th>{{ $totalKeseluruhan->total_ibu_hamil }}</th>
                                    <th>{{ $totalKeseluruhan->total_ibu_menyusui }}</th>
                                    <th>{{ $totalKeseluruhan->total_lansia }}</th>
                                    <td>{{ $totalKeseluruhan->total_buta_baca }}</td>
                                    <td>{{ $totalKeseluruhan->total_buta_tulis }}</td>
                                    <td>{{ $totalKeseluruhan->total_buta_hitung }}</td>
                                    <th>{{ $totalKeseluruhan->total_difabel }}</th>

                                    <td>{{ $totalKeseluruhan->jumlah_layak_huni }}</td>
                                    <td>{{ $totalKeseluruhan->jumlah_tidak_layak_huni }}</td>
                                    <td>{{ $totalKeseluruhan->total_tempat_sampah_keluarga }}</td>
                                    <td>{{ $totalKeseluruhan->total_saluran_air_limbah }}</td>
                                    <td>{{ $totalKeseluruhan->total_jamban_keluarga }}</td>
                                    <td>{{ $totalKeseluruhan->total_jamban_keluarga_jumlah }}</td>
                                    <td>{{ $totalKeseluruhan->total_stiker_p4k }}</td>

                                    <td>{{ $totalKeseluruhan->jumlah_pdam }}</td>
                                    <td>{{ $totalKeseluruhan->jumlah_sumur }}</td>
                                    <td>{{ $totalKeseluruhan->jumlah_sumber_air_lain }}</td>

                                    <td>{{ $totalKeseluruhan->jumlah_makanan_pokok }}</td>
                                    <td>{{ $totalKeseluruhan->jumlah_makanan_pokok_lain }}</td>
                                    
                                    <td>{{ $totalKeseluruhan->total_aktivitas_up2k }}</td>
                                    <td>{{ $totalKeseluruhan->jumlah_aktivitas_up2k_lain }}</td>
                                    <td>{{ $totalKeseluruhan->total_memiliki_tabungan }}</td>
                                    <td>{{ $totalKeseluruhan->total_aktivitas_usaha_kesehatan_lingkungan }}</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                            
                        </table>
                    </div>

                    <div class="card-footer text-center" style="border-radius: 15px;">
                        <div class="btn-group" role="group">
                            <a href="{{ route('superadmin.laporan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                Kembali
                            </a>

                            <a href="{{ route('superadmin.laporan.desa.statistikkecamatan', [
                                'no_prop' => $data->no_prop,
                                'no_kab' => $data->no_kab,
                                'no_kec' => $data->no_kec
                            ]) }}" class="btn btn-primary">
                                <i class="fas fa-fw fa-chart-bar"></i>
                                Statistik
                            </a>

                            <a href="{{ route('superadmin.laporan.desa.laporanDesPDF', [
                                'no_prop' => $data->no_prop,
                                'no_kab' => $data->no_kab,
                                'no_kec' => $data->no_kec
                            ]) }}" class="btn btn-success">
                                <i class="fas fa-download fa-sm text-white-50"></i> Download PDF
                            </a>

                        </div>
                    </div>
               
                </div>
            </div>
        </div>
    </div>    
<!---</div>--->
@endsection

