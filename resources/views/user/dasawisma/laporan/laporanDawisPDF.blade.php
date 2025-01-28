<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Akumulasi Dasawisma</title>
    <style>
        /* Mengatur ukuran font dan margin */
        body {
            font-family: Arial, sans-serif;
            font-size: 6px;  /* Memperkecil ukuran font lebih jauh */
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;  /* Mengatur tabel agar kolom tidak berubah ukuran */
        }

        th, td {
            padding: 2px 4px;  /* Padding lebih kecil untuk memberikan lebih banyak ruang */
            border: 1px solid #000;
            text-align: center;
            font-size: 5px;  /* Ukuran font lebih kecil */
            line-height: 1.2;
            word-wrap: break-word;
            white-space: normal;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        table thead {
            display: table-header-group;
        }

        table tfoot {
            display: table-footer-group;
        }

        tr {
            page-break-inside: avoid;
        }

        @media print {
            body {
                font-size: 6px;
            }
            
            table {
                page-break-before: always;
            }
        }

    </style>
</head>
<body>
    <div class="card-header py-3">
        <h2 class="font-weight-bold text-primary">AKUMULASI DASAWISMA</h2>
    </div>
    <table class="table table-bordered table-striped table-hover shadow" style="border-radius: 15px;">
        <thead class="bg-primary text-white">
            <tr>
                <th class="text-center align-middle sticky-header" rowspan="2">No</th>
                <th class="text-center align-middle sticky-header" rowspan="2">Kecamatan</th>
                <th class="text-center align-middle sticky-header" rowspan="2">JML KK</th>
                <th class="text-center align-middle sticky-header" colspan="11">Data Keluarga</th>
                <th class="text-center align-middle sticky-header" colspan="7">Kriteria Rumah</th>
                <th class="text-center align-middle sticky-header" colspan="3">Sumber Air Keluarga</th>
                <th class="text-center align-middle sticky-header" colspan="2">Makanan Pokok</th>
                <th class="text-center align-middle sticky-header" colspan="4">Warga Mengikuti Kegiatan</th>
            </tr>
            <tr>
                <th class="sticky-subheader">Jumlah Anggota Keluarga</th>
                <th class="sticky-subheader">Total Balita</th>
                <th class="sticky-subheader">Pasangan Usia Subur</th>
                <th class="sticky-subheader">Wanita Usia Subur</th>
                <th class="sticky-subheader">Ibu Hamil</th>
                <th class="sticky-subheader">Ibu Menyusui</th>
                <th class="sticky-subheader">Lansia</th>
                <th class="sticky-subheader">Buta Baca</th>
                <th class="sticky-subheader">Buta Tulis</th>
                <th class="sticky-subheader">Buta Hitung</th>
                <th class="sticky-subheader">Berkebutuhan Khusus</th>
                <!-- Kriteria Rumah -->
                <th class="sticky-subheader">Layak Huni</th>
                <th class="sticky-subheader">Tidak Layak Huni</th>
                <th class="sticky-subheader">Pembuangan Sampah</th>
                <th class="sticky-subheader">Saluran Limbah</th>
                <th class="sticky-subheader">Jamban Keluarga</th>
                <th class="sticky-subheader">Jumlah Jamban</th>
                <th class="sticky-subheader">Stiker P4K</th>
                <!-- Sumber Air -->
                <th class="sticky-subheader">PDAM</th>
                <th class="sticky-subheader">Sumur</th>
                <th class="sticky-subheader">Sumber Lain</th>
                <!-- Makanan Pokok -->
                <th class="sticky-subheader">Beras</th>
                <th class="sticky-subheader">Non Beras</th>
                <!-- Kegiatan -->
                <th class="sticky-subheader">Aktivitas UP2K</th>
                <th class="sticky-subheader">Aktivitas Lain</th>
                <th class="sticky-subheader">Tabungan</th>
                <th class="sticky-subheader">Kesehatan Lingkungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dawisData as $index => $dawis)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $dawis->nama_dawis }}</td>
                    <td>{{ $dawis->jumlah_kepala_keluarga }}</td>
                    <td>{{ $dawis->total_jumlah_anggota_keluarga }}</td>
                    <td>{{ $dawis->total_balita }}</td>
                    <td>{{ $dawis->total_pus }}</td>
                    <td>{{ $dawis->total_wus }}</td>
                    <td>{{ $dawis->total_ibu_hamil }}</td>
                    <td>{{ $dawis->total_ibu_menyusui }}</td>
                    <td>{{ $dawis->total_lansia }}</td>
                    <td>{{ $dawis->total_buta_baca }}</td>
                    <td>{{ $dawis->total_buta_tulis }}</td>
                    <td>{{ $dawis->total_buta_hitung }}</td>
                    <td>{{ $dawis->total_difabel }}</td>
                    <td>{{ $dawis->jumlah_layak_huni }}</td>
                    <td>{{ $dawis->jumlah_tidak_layak_huni }}</td>
                    <td>{{ $dawis->total_tempat_sampah_keluarga }}</td>
                    <td>{{ $dawis->total_saluran_air_limbah }}</td>
                    <td>{{ $dawis->total_jamban_keluarga }}</td>
                    <td>{{ $dawis->total_jamban_keluarga_jumlah }}</td>
                    <td>{{ $dawis->total_stiker_p4k }}</td>
                    <td>{{ $dawis->jumlah_pdam }}</td>
                    <td>{{ $dawis->jumlah_sumur }}</td>
                    <td>{{ $dawis->jumlah_sumber_air_lain }}</td>
                    <td>{{ $dawis->jumlah_makanan_pokok }}</td>
                    <td>{{ $dawis->jumlah_makanan_pokok_lain }}</td>
                    <td>{{ $dawis->total_aktivitas_up2k }}</td>
                    <td>{{ $dawis->jumlah_aktivitas_up2k_lain }}</td>
                    <td>{{ $dawis->total_memiliki_tabungan }}</td>
                    <td>{{ $dawis->total_aktivitas_usaha_kesehatan_lingkungan }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot class="bg-primary text-white">
            <tr class="text-center align-middle">
                <th colspan="2" class="text-center align-middle">TOTAL</th>
                <td>{{ $totals['jumlah_kepala_keluarga'] }}</td>
                <td>{{ $totals['total_jumlah_anggota_keluarga'] }}</td>
                <td>{{ $totals['total_balita'] }}</td>
                <td>{{ $totals['total_pus'] }}</td>
                <td>{{ $totals['total_wus'] }}</td>
                <td>{{ $totals['total_ibu_hamil'] }}</td>
                <td>{{ $totals['total_ibu_menyusui'] }}</td>
                <td>{{ $totals['total_lansia'] }}</td>
                <td>{{ $totals['total_buta_baca'] }}</td>
                <td>{{ $totals['total_buta_tulis'] }}</td>
                <td>{{ $totals['total_buta_hitung'] }}</td>
                <td>{{ $totals['total_difabel'] }}</td>
                <td>{{ $totals['jumlah_layak_huni'] }}</td>
                <td>{{ $totals['jumlah_tidak_layak_huni'] }}</td>
                <td>{{ $totals['total_tempat_sampah_keluarga'] }}</td>
                <td>{{ $totals['total_saluran_air_limbah'] }}</td>
                <td>{{ $totals['total_jamban_keluarga'] }}</td>
                <td>{{ $totals['total_jamban_keluarga_jumlah'] }}</td>
                <td>{{ $totals['total_stiker_p4k'] }}</td>
                <td>{{ $totals['jumlah_pdam'] }}</td>
                <td>{{ $totals['jumlah_sumur'] }}</td>
                <td>{{ $totals['jumlah_sumber_air_lain'] }}</td>
                <td>{{ $totals['jumlah_makanan_pokok'] }}</td>
                <td>{{ $totals['jumlah_makanan_pokok_lain'] }}</td>
                <td>{{ $totals['total_aktivitas_up2k'] }}</td>
                <td>{{ $totals['jumlah_aktivitas_up2k_lain'] }}</td>
                <td>{{ $totals['total_memiliki_tabungan'] }}</td>
                <td>{{ $totals['total_aktivitas_usaha_kesehatan_lingkungan'] }}</td>
            </tr>
        </tfoot>
        
    </table>
</body>
</html>
