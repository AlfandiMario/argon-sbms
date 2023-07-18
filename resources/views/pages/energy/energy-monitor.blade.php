@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => 'Energy'])
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    @include('pages.energy.nav')
                    <div class="card-header my-0 py-0">
                        <h6>Energy Usage</h6>
                    </div>
                    <div class="card-body pt-0 mb-0">
                        <div class="d-flex justify-content-center">
                            @php
                            $collection = ["Yesterday", "This Month", "Tariff", "This Month Cost", "Last Month Cost"];
                            $value = ["4 kWh", "15 kWh", "1440 IDR", "120k", "300k"];
                            $i = 0;
                            foreach ($collection as $item):
                            @endphp
                            <div class="col mx-2 p-2 border border-shadow" style="border-radius: 1rem; background-color:white">
                                <div class="text-center">
                                    <p class="text-sm mb-2 text-uppercase font-weight-bold">
                                    @php echo $item; @endphp
                                    </p>
                                    <h6 class="font-weight-bolder text-warning">
                                        @php echo $value[$i]; @endphp
                                    </h6>
                                </div>
                            </div>
                            @php 
                            $i++;
                            endforeach @endphp
                        </div>
                    </div>
                    <div class="card-header my-0 py-0">
                        <h6>Real Time Monitoring</h6>
                    </div>
                    
                    <div class="card-body pt-0">
                        {{-- Section Real Time --}}
                        <div class="d-flex justify-content-center">
                            @php
                            $collection = ["Voltage", "Ampere", "Frequency" ,  "Power", "Reactive Power", "Apparent Power" ];
                            $value = ["220 V", "1 A", "60 Hz", "300 W", "300 W", "300 W", "60 Hz" ];
                            for ($i=0; $i< 6; $i++) :  
                            @endphp
                            <div class="col mx-2 p-2 border border-shadow" style="border-radius: 1rem; background-color:white">
                                <div class="numbers text-center">
                                    <p class="text-sm mb-2 text-uppercase font-weight-bold">
                                    @php echo $collection[$i]; @endphp
                                    </p>
                                    <h6 class="font-weight-bolder text-warning">
                                        @php echo $value[$i]; @endphp
                                    </h6>
                                </div>
                            </div>
                            @php
                            endfor
                            @endphp
                        </div>

                        {{-- Section Graph --}}
                        <div class="row mt-4">
                            <div class="col-lg-12 mb-lg-0 mb-4">
                                <div class="card z-index-2 h-100">
                                    <div class="card-header pb-0 pt-3 bg-transparent">
                                        <h6 class="text-capitalize">Power Consumption (Day-to-day)</h6>
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
                        <div class="row mt-4">
                            <div class="col-lg-12 mb-lg-0 mb-4">
                                <div class="card z-index-2 h-100">
                                    <div class="card-header pb-0 pt-3 bg-transparent">
                                        <h6 class="text-capitalize">Energy Consumption (Day-to-day)</h6>
                                        <p class="text-sm mb-0">
                                            <i class="fa fa-arrow-up text-success"></i>
                                            <span class="font-weight-bold ">4% more</span> than previous month
                                        </p>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="chart">
                                            <canvas id="chart-line-2" class="chart-canvas" height="300"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Projects table</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center justify-content-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Project</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Budget</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Status</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">
                                            Completion</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2">
                                                <div>
                                                    <img src="/img/small-logos/logo-spotify.svg"
                                                        class="avatar avatar-sm rounded-circle me-2" alt="spotify">
                                                </div>
                                                <div class="my-auto">
                                                    <h6 class="mb-0 text-sm">Spotify</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">$2,500</p>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">working</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2 text-xs font-weight-bold">60%</span>
                                                <div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-info" role="progressbar"
                                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                                            style="width: 60%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2">
                                                <div>
                                                    <img src="/img/small-logos/logo-invision.svg"
                                                        class="avatar avatar-sm rounded-circle me-2" alt="invision">
                                                </div>
                                                <div class="my-auto">
                                                    <h6 class="mb-0 text-sm">Invision</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">$5,000</p>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">done</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2 text-xs font-weight-bold">100%</span>
                                                <div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-success" role="progressbar"
                                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                            style="width: 100%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2">
                                                <div>
                                                    <img src="/img/small-logos/logo-jira.svg"
                                                        class="avatar avatar-sm rounded-circle me-2" alt="jira">
                                                </div>
                                                <div class="my-auto">
                                                    <h6 class="mb-0 text-sm">Jira</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">$3,400</p>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">canceled</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2 text-xs font-weight-bold">30%</span>
                                                <div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-danger" role="progressbar"
                                                            aria-valuenow="30" aria-valuemin="0" aria-valuemax="30"
                                                            style="width: 30%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2">
                                                <div>
                                                    <img src="/img/small-logos/logo-slack.svg"
                                                        class="avatar avatar-sm rounded-circle me-2" alt="slack">
                                                </div>
                                                <div class="my-auto">
                                                    <h6 class="mb-0 text-sm">Slack</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">$1,000</p>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">canceled</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2 text-xs font-weight-bold">0%</span>
                                                <div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-success" role="progressbar"
                                                            aria-valuenow="0" aria-valuemin="0" aria-valuemax="0"
                                                            style="width: 0%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2">
                                                <div>
                                                    <img src="/img/small-logos/logo-webdev.svg"
                                                        class="avatar avatar-sm rounded-circle me-2" alt="webdev">
                                                </div>
                                                <div class="my-auto">
                                                    <h6 class="mb-0 text-sm">Webdev</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">$14,000</p>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">working</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2 text-xs font-weight-bold">80%</span>
                                                <div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-info" role="progressbar"
                                                            aria-valuenow="80" aria-valuemin="0" aria-valuemax="80"
                                                            style="width: 80%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2">
                                                <div>
                                                    <img src="/img/small-logos/logo-xd.svg"
                                                        class="avatar avatar-sm rounded-circle me-2" alt="xd">
                                                </div>
                                                <div class="my-auto">
                                                    <h6 class="mb-0 text-sm">Adobe XD</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm font-weight-bold mb-0">$2,300</p>
                                        </td>
                                        <td>
                                            <span class="text-xs font-weight-bold">done</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <span class="me-2 text-xs font-weight-bold">100%</span>
                                                <div>
                                                    <div class="progress">
                                                        <div class="progress-bar bg-gradient-success" role="progressbar"
                                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                                                            style="width: 100%;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle">
                                            <button class="btn btn-link text-secondary mb-0" aria-haspopup="true"
                                                aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        {{-- @include('layouts.footers.auth.footer') --}}
    </div>
@endsection
@push('js')
    <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
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
                    borderColor: "#596CFF",
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

        var ctx2 = document.getElementById("chart-line-2").getContext("2d");

        var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

        gradientStroke1.addColorStop(1, 'rgba(251, 99, 64, 0.2)');
        gradientStroke1.addColorStop(0.2, 'rgba(251, 99, 64, 0.0)');
        gradientStroke1.addColorStop(0, 'rgba(251, 99, 64, 0)');
        new Chart(ctx2, {
            type: "line",
            data: {
                labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
                datasets: [{
                    label: "Energy (kWh)",
                    tension: 0.4,
                    borderWidth: 0,
                    pointRadius: 0,
                    borderColor: "#63B3ED",
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