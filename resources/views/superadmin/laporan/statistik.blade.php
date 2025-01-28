@extends('superadmin.admin')

@section('main-content')

<div class="row">
    <div class="col-xl-12 mb-4">
        <div class="card shadow-lg h-100 py-2" style="border-radius: 15px;">
            
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistik Dasawisma Kabupaten Jepara</h6>
            </div>

            <!-- Card for Chart -->
            <div class="card-body">
                <div class="chart-area" style="position: relative; height: 500px;">
                    <canvas id="chartBarTotalAll"></canvas>
                </div>
            </div>
        
            <div class="card-footer text-center" style="border-radius: 15px;">
                <div class="btn-group" role="group">
                    <a href="{{ route('superadmin.laporan.index') }}" class="btn btn-primary">
                                    <i class="fas fa-fw fa-table"></i>
                                    Table Data
                                
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    var ctx = document.getElementById('chartBarTotalAll').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($dataChartBarTotalAll['labels']),
            datasets: [{
                label: 'Statistik Total Keseluruhan',
                data: @json($dataChartBarTotalAll['data']),
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
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
        plugins: [ChartDataLabels] // Enable the plugin for data labels
    });
</script>


<div class="d-flex justify-content-start mt-4">
    <div class="row">
        @foreach($dataChartBarPerItem['datasets'] as $dataset)
        <div class="col-xl-6 col-lg-7 mb-4">
            <!-- Bar Chart -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bar Chart: {{ $dataset['label'] }} Per Kecamatan</h6>
                </div>
                <div class="card-body" style="position: relative; height: 400px;">
                    <div class="chart-area">
                        <canvas id="{{ $dataset['chartId'] }}"></canvas> 

                        <button class="btn btn-primary btn-sm mt-2" onclick="toggleTable('{{ $dataset['chartId'] }}')">Lihat Detail Tabel</button>
                    </div>
                    <!-- Button to toggle table visibility -->
                    
                </div>
            </div>
            
            <!-- Hidden Table -->
            <div class="card shadow mt-3" id="table-{{ $dataset['chartId'] }}" style="display: none;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tabel Data: {{ $dataset['label'] }} Per Kecamatan</h6>
                </div>
                <div class="card-body" style="position: relative; height: 600px;">
                    <table class="table table-bordered table-striped table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kecamatan</th>
                                <th>{{ $dataset['label'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dataChartBarPerItem['labels'] as $index => $label)
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

    @foreach ($dataChartBarPerItem['datasets'] as $dataset)
    const data{{ $loop->index }} = {
        labels: {!! json_encode($dataChartBarPerItem['labels']) !!},
        datasets: [{
            label: "{{ $dataset['label'] }}",
            data: {!! json_encode($dataset['data']) !!},
            backgroundColor: "{{ $dataset['backgroundColor'] }}",
            hoverBackgroundColor: "{{ $dataset['hoverBackgroundColor'] }}",
            borderColor: "{{ $dataset['borderColor'] }}",
            borderWidth: {{ $dataset['borderWidth'] }}
        }]
    };

    const config{{ $loop->index }} = {
        type: 'bar',
        data: data{{ $loop->index }},
        options: {
            responsive: true, // Membuat chart responsif
            maintainAspectRatio: false, // Memastikan chart menyesuaikan ukuran kontainer
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
                        stepSize: 1,
                        callback: function(value) {
                            return value;
                        }
                    }
                }
            }
        }
    };

    var ctx{{ $loop->index }} = document.getElementById("{{ $dataset['chartId'] }}").getContext('2d');
    if (ctx{{ $loop->index }}) {
        new Chart(ctx{{ $loop->index }}, config{{ $loop->index }});
    }
    @endforeach
</script>




@endsection


