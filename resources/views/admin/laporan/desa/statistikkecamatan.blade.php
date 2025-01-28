@extends('admin.admin')  <!-- Menggunakan layout AdminLTE atau layout yang sudah ada -->

@section('main-content')

<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">

            <div class="card-header">
                <h6 class="m-0 font-weight-bold text-primary">Statistik Dasawisma Kabupaten Jepara</h6>
            </div>
            <!-- Card for Chart -->
            <div class="card-body">
                <div class="chart-area" style="position: relative; height: 600px;">
                    <canvas id="statistikChart" width="400" height="200"></canvas>
                </div>
            </div>

            <div class="card-footer text-center" style="border-radius: 15px;">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.laporan.desa.index', ['no_prop' => $no_prop, 'no_kab' => $no_kab, 'no_kec' => $no_kec]) }}" class="btn btn-primary">
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
    var ctx = document.getElementById('statistikChart').getContext('2d');
    var statistikChart = new Chart(ctx, {
        type: 'bar', // Jenis chart (bar, line, pie, dll)
        data: {
            labels: @json($chartData['labels']),  // Labels untuk sumbu x
            datasets: [{
                label: 'Jumlah',
                data: @json($chartData['values']),  // Data untuk sumbu y
                backgroundColor: '#4e73df',  // Warna background bar
                borderColor: '#4e73df',  // Warna border bar
                borderWidth: 1
            }]
        },
        options: {
            plugins: {
                datalabels: {
                    color: '#000',  // Black label color
                    anchor: 'end',
                    align: 'top',
                    formatter: (value) => value.toLocaleString(), // Format number
                    font: {
                        weight: 'bold',
                        size: 12
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
        plugins: [ChartDataLabels] // Enable the plugin for data label
    });
</script>



<div class="d-flex justify-content-start mt-4">
    <div class="row">
        @foreach($chartDataDesaPerItem['datasets'] as $dataset)
        <div class="col-xl-6 col-lg-7 mb-4">
            <!-- Bar Chart -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bar Chart: {{ $dataset['label'] }} Per Desa</h6>
                </div>
                <div class="card-body" style="position: relative; height: 320px;">
                    <div class="chart-bar">
                        <canvas id="{{ $dataset['chartId'] }}"></canvas> 
                        
                        <button class="btn btn-primary btn-sm mt-2" onclick="toggleTable('{{ $dataset['chartId'] }}')">Lihat Detail Tabel</button>
                    </div>
                </div>
                
            </div>

            <!-- Hidden Table -->
            <div class="card shadow mt-3" id="table-{{ $dataset['chartId'] }}" style="display: none;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tabel Data: {{ $dataset['label'] }} Per Desa</h6>
                </div>
                <div class="card-body" style="position: relative; height: 220px;">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Desa</th>
                                <th>{{ $dataset['label'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chartDataDesaPerItem['labels'] as $index => $label)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $label }}</td>
                                <td>{{ $dataset['data'][$index] }}</td>
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
    #table-{{ $dataset['chartId'] }} {
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
    function toggleTable(chartId) {
            var table = document.getElementById("table-" + chartId);
            if (table.style.display === "none") {
                table.style.display = "block";  // Show the table
            } else {
                table.style.display = "none";  // Hide the table
            }
        }

    const labels = @json($chartDataDesaPerItem['labels']);

    @foreach($chartDataDesaPerItem['datasets'] as $dataset)
    new Chart(document.getElementById('{{ $dataset['chartId'] }}'), {
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
                x: {
                    beginAtZero: true
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,  // Pastikan langkahnya 1
                        callback: function(value) {
                            // Menampilkan angka bulat pada sumbu Y
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