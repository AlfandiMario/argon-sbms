@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => $title])
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                @include('pages.energy.nav')
                <div class="card-body pt-0">
                    {{-- Section Graph --}}
                    <div class="row mt-4">
                        <div class="col-lg-12 mb-lg-0 mb-4">
                            <div class="card z-index-2 h-100">
                                <div class="card-header pb-0 pt-3 bg-transparent">
                                    <h6 class="text-capitalize">Energy Consumption (Daily)</h6>
                                    <p class="text-sm mb-0">
                                        <i class="fa fa-arrow-up text-success"></i>
                                        <span class="font-weight-bold ">4% more</span> than previous month
                                    </p>
                                </div>
                                <div class="card-body p-3">
                                    <div class="chart">
                                        <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>


<script>
    var dates = JSON.parse('{!! json_encode($dates) !!}');
    var energies = JSON.parse('{!! json_encode($dailyEnergy) !!}');

    var ctx1 = document.getElementById("chart-line").getContext("2d");

    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(251, 99, 64, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(251, 99, 64, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(251, 99, 64, 0)');
    new Chart(ctx1, {
        type: "line",
        data: {
            labels: dates,
            datasets: [{
                label: "Energy (kWh)",
                tension: 0.2,
                borderWidth: 0,
                pointRadius: 2,
                borderColor: "#63B3ED",
                backgroundColor: gradientStroke1,
                borderWidth: 3,
                fill: true,
                data: energies,
                maxBarThickness: 6
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: false
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: function (ike) {
                            if (ike.tick.value <= 7920) {
                                return '#00ff00'
                            } else if (7920 < ike.tick.value < 12080) {
                                return '#009900'
                            } else if (12080 < ike.tick.value < 14580) {
                                return '#ffff00'
                            } else if (14580 < ike.tick.value < 19170) {
                                return '#ff9900'
                            } else if (19170 < ike.tick.value < 23750) {
                                return '#ff3300'
                            }
                            else {
                                return '#800000'
                            }

                        }
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#aaa',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: true,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#aaa',
                        padding: 20,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
            animation: {
            },
        },
    });
</script>
@endpush