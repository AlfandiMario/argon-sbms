@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => $title])
<div class="container-fluid py-4">
    <div class="row">
        <div class="card">
            <div class="row-12 my-4">
                <div class="card-header my-0 py-0">
                    <h6>Energy Usage</h6>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex justify-content-center">
                        @php
                        $i = 0;
                        foreach ($collection as $item):
                        @endphp
                        <div class="col mx-2 p-2 border border-shadow"
                            style="border-radius: 1rem; background-color:white">
                            <div class="numbers text-center">
                                <p class="text-sm mb-2 text-uppercase font-weight-bold">
                                    @php echo $item; @endphp
                                </p>
                                <h6 class="font-weight-bolder text-warning">
                                    @php echo
                                    number_format($values[$i],2,',','.'); @endphp
                                </h6>
                            </div>
                        </div>
                        @php
                        $i++;
                        endforeach @endphp
                    </div>
                </div>
            </div>

            {{-- Section Graph --}}
            <div class="row">
                <div class="col-lg-12 mb-lg-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent">
                            <h6 class="text-capitalize">Energy Consumption (Monthly)</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-arrow-up text-success"></i>
                                <span class="font-weight-bold ">4% more</span> than previous month
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="chart-monthly" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4 mb-2">
                <div class="col-lg-12 mb-lg-0 mb-4">
                    <div class="card z-index-2 h-100">
                        <div class="card-header pb-0 pt-3 bg-transparent">
                            <h6 class="text-capitalize">Energy Consumption (Annualy)</h6>
                            <p class="text-sm mb-0">
                                <i class="fa fa-arrow-up text-success"></i>
                                <span class="font-weight-bold ">4% more</span> than previous month
                            </p>
                        </div>
                        <div class="card-body p-3">
                            <div class="chart">
                                <canvas id="chart-annual" class="chart-canvas" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footers.auth.footer')
</div>
@endsection
@push('js')
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script>
    var month = JSON.parse('{!! json_encode($month) !!}');
    var monthlyEnergy = JSON.parse('{!! json_encode($monthlyEnergy) !!}');
    var ike = JSON.parse('{!! json_encode($ike) !!}');
    var color = JSON.parse('{!! json_encode($color) !!}');
    // console.log(ike);

    var ctx = document.getElementById("chart-monthly").getContext("2d");

    var gradientStroke1 = ctx.createLinearGradient(0, 230, 0, 50);

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: month,
            datasets: [{
                label: 'Energy (kWh)',
                data: monthlyEnergy,
                backgroundColor: color,
                borderColor: color,
                borderWidth: 1
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    enabled: 'false'
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
                        borderDash: [5, 5],
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    },
                    title: {
                        display: true,
                        text: 'Energy (kWh)',
                        font: {
                            family: 'Open Sans',
                            style: 'normal',
                            lineHeight: 1
                        },
                        padding: { top: 0, left: 0, right: 0, bottom: 0 }
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
                        padding: 20,
                        font: {
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    },
                },
            },
            animation: {
                onProgress: function () {
                    var chart = this;
                    var ctx = chart.ctx;
                    ctx.font = Chart.helpers.fontString(Chart.defaults.font.size, Chart.defaults.font.style, Chart.defaults.font.family);
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';

                    chart.data.datasets.forEach(function (dataset, i) {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = ike[index];
                            ctx.fillStyle = 'black';
                            ctx.fillText(data, bar.x, bar.y - 5);
                        });
                    });
                }
            }
        },
    });

    var year = JSON.parse('{!! json_encode($year) !!}');
    var annualEnergy = JSON.parse('{!! json_encode($annualEnergy) !!}');
    var ike_y = JSON.parse('{!! json_encode($ike_y) !!}');
    var color_y = JSON.parse('{!! json_encode($color_y) !!}');

    var ctx2 = document.getElementById("chart-annual").getContext("2d");

    var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

    var annualChart = new Chart(ctx2, {
        type: "bar",
        data: {
            labels: year,
            datasets: [{
                label: 'Energy (kWh)',
                data: annualEnergy,
                backgroundColor: color_y,
                borderColor: color_y,
                borderWidth: 1
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
                tooltip: {
                    enabled: 'false'
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
                        borderDash: [5, 5],
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    },
                    title: {
                        display: true,
                        text: 'Energy (kWh)',
                        font: {
                            family: 'Open Sans',
                            style: 'normal',
                            lineHeight: 1
                        },
                        padding: { top: 0, left: 0, right: 0, bottom: 0 }
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
                        padding: 20,
                        font: {
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    },
                },
            },
            animation: {
                onProgress: function () {
                    var chart = this;
                    var ctx = chart.ctx;
                    ctx.font = Chart.helpers.fontString(Chart.defaults.font.size, Chart.defaults.font.style, Chart.defaults.font.family);
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'bottom';

                    chart.data.datasets.forEach(function (dataset, i) {
                        var meta = chart.getDatasetMeta(i);
                        meta.data.forEach(function (bar, index) {
                            var data = ike_y[index];
                            ctx.fillStyle = 'black';
                            ctx.fillText(data, bar.x, bar.y - 5);
                        });
                    });
                }
            }
        },
    });
    // Set the bar thickness (width) in pixels
    annualChart.data.datasets[0].barThickness = 60;
    annualChart.update(); // Update the chart to apply the changes
</script>
@endpush