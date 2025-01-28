@extends('superadmin.admin') {{-- Sesuaikan dengan layout Anda --}}

@section('main-content')
<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">

           
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">STATISTIK DASAWISMA DESA : {{ $namaDesa }}</h6>
                </div>

                <!-- Card for Chart -->
                <div class="card-body" style="border-radius: 15px;">
                    <div class="chart-bar" style="position: relative; height: 500px;">
                        <canvas id="desaChart" width="400" height="200"></canvas>
                    </div>
                </div>
               

                <div class="card-footer text-center" style="border-radius: 15px;">
                    <div class="btn-group" role="group">
                        <a href="{{ route('superadmin.laporan.desa.dawisdesa.index', [
                                                    'no_prop' => $no_prop,
                                                    'no_kab' => $no_kab,
                                                    'no_kec' => $no_kec,
                                                    'no_kel' => $no_kel
                                                ]) }}" class="btn btn-primary">
                            <i class="fas fa-fw fa-table"></i>
                            Table Data
                        </a>
                    </div>
                </div>
        </div>
    </div>
</div>

<script>
    // Mengambil data untuk chart
    var ctx = document.getElementById('desaChart').getContext('2d');
    var desaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']), // Labels untuk sumbu x
            datasets: [{
                label: 'TOTAL STATISTIK DESA {{$namaDesa}}',
                data: @json($chartData['values']), // Data untuk sumbu y
                backgroundColor: '#36b9cc',
                borderColor: '#36b9cc',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                datalabels: {
                    anchor: 'end', // Posisi di ujung balok
                    align: 'top', // Label sejajar di atas
                    offset: 2, // Jarak antara label dan balok
                    formatter: function(value) {
                        return value; // Menampilkan nilai data
                    },
                    color: '#000', // Warna teks
                    font: {
                        weight: 'bold'
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value % 1 === 0 ? value : null;
                        }
                    },
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                },
                x: {
                    title: {
                        display: true,
                    }
                }
            }
        },
        plugins: [ChartDataLabels] // Tambahkan plugin DataLabels di sini
    });
</script>

<div class="d-flex justify-content-start mt-4">
    <div class="row">
        @foreach($chartDataDawisPerItem['datasets'] as $index => $dataset)
        <div class="col-xl-6 col-lg-7 mb-4">
            <!-- Bar Chart -->
            <div class="card shadow">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bar Chart: {{ $dataset['label'] }} Per Dawis</h6>
                </div>

                <div class="card-body" style="position: relative; height: 320px;">
                    <div class="chart-area">
                        <canvas id="chart-{{ $index }}"></canvas>
                        <button class="btn btn-primary btn-sm mt-2" onclick="toggleTable('{{ $index }}')">Lihat Detail Tabel</button>
                    </div>
                </div>

            </div>

            <!-- Hidden Table -->
            <div class="card shadow mt-3" id="table-{{ $index }}" style="display: none;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tabel Data: {{ $dataset['label'] }} Per Dawis</h6>
                </div>
                <div class="card-body" style="position: relative; height: 220px;">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Dawis</th>
                                <th>{{ $dataset['label'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataset['data'] as $key => $value)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $dataset['label'] }}</td>
                                <td>{{ $value }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    .table-sm {
        font-size: 0.9rem; /* Menyesuaikan ukuran font dalam tabel */
    }
    
    /* Mengurangi padding di dalam tabel */
    .table-sm th, .table-sm td {
        padding: 5px;
    }
    
    /* Mengatur ukuran font pada baris tabel */
    .table-sm td, .table-sm th {
        text-align: center;
    }

    /* Mengatur tinggi dan lebar agar tabel lebih kompak */
    .table-dawis {
        font-size: 0.9rem;
        max-height: 300px; /* Mengatur tinggi tabel */
        overflow-y: auto; /* Menambahkan scroll jika tabel terlalu besar */
    }

    /* Memberikan sedikit pewarnaan pada baris ganjil tabel */
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    // Function to toggle the visibility of the table
    function toggleTable(index) {
        var table = document.getElementById("table-" + index);
        if (table.style.display === "none") {
            table.style.display = "block";  // Show the table
        } else {
            table.style.display = "none";  // Hide the table
        }
    }

    const labels = @json($chartDataDawisPerItem['labels']);

    @foreach($chartDataDawisPerItem['datasets'] as $index => $dataset)
    new Chart(document.getElementById('chart-{{ $index }}'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: '{{ $dataset['label'] }}',
                data: @json($dataset['data']),
                backgroundColor: '{{ $dataset['backgroundColor'] }}',
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    enabled: true,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        callback: function(value) {
                            return value;
                        }
                    }
                }
            }
        }
    });
    @endforeach
</script>

@endsection
