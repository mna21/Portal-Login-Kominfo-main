@extends('user.admin') {{-- Sesuaikan dengan layout Anda --}}

@section('main-content')


<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
            
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">STATISTIK DASAWISMA DESA</h6>
            </div>

            <!-- Card for Chart -->
            <div class="card-body">
                <div class="chart-area" style="position: relative; height: 500px;">
                    <canvas id="desaChart"></canvas>
                </div>
            </div>

            <div class="card-footer text-center" style="border-radius: 15px;">
                <div class="btn-group" role="group">
                    <a href="{{ route('user.dasawisma.laporan.index') }}" class="btn btn-primary">
                        <i class="fas fa-fw fa-table"></i>
                        Table Data
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>


<script>
    var ctx = document.getElementById('desaChart').getContext('2d');
    var desaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'TOTAL STATISTIK DESA',
                data: @json($chartData['values']),
                backgroundColor: '#4e73df', // SB Admin 2 Blue
                hoverBackgroundColor: '#2e59d9', // Darker Blue for hover
                borderColor: '#4e73df', // Blue border
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, // Membuat chart responsif
            maintainAspectRatio: false, // Memastikan chart menyesuaikan ukuran kontainer
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
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah',
                            padding: 20,
                            font: {
                                size: 16
                            },
                            // Mengatur orientasi label Y agar vertikal
                            rotation: -90
                        }
                    }
                }
            }
        },
        plugins: [ChartDataLabels] // Enable the plugin for data labels
    });
</script>




<div class="d-flex justify-content-start mt-4">
    <div class="row">
        @foreach($chartDataDawisPerItem['datasets'] as $index => $dataset)
        <div class="col-xl-6 col-lg-7 mb-4">
            <!-- Bar Chart -->
            <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bar Chart: {{ $dataset['label'] }} Per Dawis</h6>
                </div>
                <div class="card-body" style="position: relative; height: 320px;">
                    <div class="chart-bar">
                        <canvas id="chart-{{ $index }}"></canvas>
                    </div>
                </div>

                <div class="card-footer text-center" style="border-radius: 15px;">
                    <div class="btn-group" role="group">
                        <button class="btn btn-primary btn-sm mt-2" onclick="toggleTable('{{ $index }}')">Lihat Detail Tabel</button>
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
                                @foreach($chartDataDawisPerItem['labels'] as $index => $label)
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
