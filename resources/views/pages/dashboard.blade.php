@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => 'Overview'])
<div class="container-fluid py-4">
    {{-- Section Real Time Info --}}
    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-2 text-uppercase font-weight-bold">Voltage</p>
                                <h5 class="font-weight-bolder">
                                    220 V
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">+ 0.4</span>
                                    <small> than average </small> 
                                    <!-- Fitur ini bisa jadi tambahan data analitik dengan cara merata-rata data pas aktif (bukan nol) -->
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end mt-2">
                            <div class="icon icon-shape bg-gradient-primary text-center rounded-circle">
                                <i class="fa-solid fa-plug-circle-bolt text-lg opacity-10" aria-hidden="true"
                                    style="color: white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-2 text-uppercase font-weight-bold">Current</p>
                                <h5 class="font-weight-bolder">
                                    2.4 A
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">+ 0.4</span>
                                    <small> than average </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end mt-2">
                            <div class="icon icon-shape bg-gradient-warning shadow-danger text-center rounded-circle">
                                <i class="fa-solid fa-bolt text-lg opacity-10" aria-hidden="true"
                                    style="color: orange"></i>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-2 text-uppercase font-weight-bold">Temperature</p>
                                <h5 class="font-weight-bolder">
                                    30 C
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">+ 0.4</span>
                                    <small> than average </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end mt-2">
                            <div class="icon icon-shape bg-gradient-danger shadow-danger text-center rounded-circle">
                                <i class="fa-solid fa-temperature-half text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-2 text-uppercase font-weight-bold">Humidity</p>
                                <h5 class="font-weight-bolder">
                                    70%
                                </h5>
                                <p class="mb-0">
                                    <span class="text-success text-sm font-weight-bolder">+ 0.4</span>
                                    <small> than average </small>
                                </p>
                            </div>
                        </div>
                        <div class="col-4 text-end mt-2">
                            <div class="icon icon-shape bg-gradient-primary shadow-danger text-center rounded-circle">
                                <i class="fa-solid fa-wind text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Section Graph --}}
    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize">Power Consumption (Monthly)</h6>
                    <p class="text-sm mb-0">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">4% more</span> than previous month
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-body pb-0 mb-0 pt-3 bg-transparent">
                    <h6 class="text-capitalize mb-0">Devices Status</h6>
                </div>
                <div class="card-body pt-3">
                    <div class="d-flex justify-content-between bg-gradient-light my-2 p-2 border-radius-md">
                        <div class="text-dark fw-bold">Main Lamp</div>
                        <span class="badge badge-sm bg-gradient-success">Online</span>
                    </div>
                    <div class="d-flex justify-content-between bg-gradient-light my-2 p-2 border-radius-md">
                        <div class="text-dark fw-bold">AC</div>
                        <span class="badge badge-sm bg-gradient-success">Online</span>
                    </div>
                    <div class="d-flex justify-content-between bg-gradient-light my-2 p-2 border-radius-md">
                        <div class="text-dark fw-bold">Second Lamp</div>
                        <span class="badge badge-sm bg-gradient-secondary">Offline</span>
                    </div>
                    <div class="d-flex justify-content-between bg-gradient-light my-2 p-2 border-radius-md">
                        <div class="text-dark fw-bold">Air Purifier</div>
                        <span class="badge badge-sm bg-gradient-success">Online</span>
                    </div>
                    <div class="d-flex justify-content-between bg-gradient-light my-2 p-2 border-radius-md">
                        <div class="text-dark fw-bold">Fridge</div>
                        <span class="badge badge-sm bg-gradient-secondary">Offline</span>
                    </div>
                    <div class="d-flex justify-content-between bg-gradient-light my-2 p-2 border-radius-md">
                        <div class="text-dark fw-bold">CCTV</div>
                        <span class="badge badge-sm bg-gradient-success">Online</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="./assets/js/plugins/chartjs.min.js"></script>
<script>
    var ctx1 = document.getElementById("chart-line").getContext("2d");

    var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

    gradientStroke1.addColorStop(1, 'rgba(251, 99, 64, 0.2)');
    gradientStroke1.addColorStop(0.2, 'rgba(251, 99, 64, 0.0)');
    gradientStroke1.addColorStop(0, 'rgba(251, 99, 64, 0)');
    new Chart(ctx1, {
        type: "line",
        data: {
            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Energy (kWh)",
                tension: 0.4,
                borderWidth: 0,
                pointRadius: 0,
                borderColor: "#fb6340",
                backgroundColor: gradientStroke1,
                borderWidth: 3,
                fill: true,
                data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                maxBarThickness: 6

            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
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
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#fbfbfb',
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
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#ccc',
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
        },
    });
</script>
@endpush