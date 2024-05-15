@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
@include('layouts.navbars.auth.topnav', ['title' => $title])
<div class="container-fluid py-4">
    <div class="row">
        <div class="card">
            @include('pages.energy.nav')
            <div class="row-12">
                <div class="card-header my-0 py-0">
                    <h6>Real Time Monitoring</h6>
                </div>

                <div class="card-body pt-0">
                    <!-- Section Real Time -->
                    <div class="d-flex justify-content-center">
                        @php
                        $i=0;
                        foreach ($keys as $key) : @endphp <div class="col mx-2 p-2 border border-shadow"
                            style="border-radius: 1rem; background-color:white">
                            <div class="numbers text-center">
                                <p class="text-sm mb-2 text-uppercase font-weight-bold">
                                    @php echo $collection[$i]; @endphp
                                </p>
                                <h6 class="font-weight-bolder text-warning">
                                    @php echo number_format($energies->$key,2,',','.') ; @endphp
                                </h6>
                            </div>
                        </div>
                        @php
                        $i++;
                        endforeach
                        @endphp
                    </div>
                </div>

                <div class="row-12">
                    <div class="card-header my-0 py-0">
                        <h6>Energy Usage</h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex justify-content-center">
                            @php
                            $i = 0;
                            foreach ($collection2 as $item):
                            @endphp
                            <div class="col mx-2 p-2 border border-shadow"
                                style="border-radius: 1rem; background-color:white">
                                <div class="numbers text-center">
                                    <p class="text-sm mb-2 text-uppercase font-weight-bold">
                                        @php echo $item; @endphp
                                    </p>
                                    <h6 class="font-weight-bolder text-warning">
                                        @php echo
                                        number_format($values2[$i],0,',','.'); @endphp
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
                <div class="row mt-4">
                    <div class="col-lg-12 mb-lg-0 mb-4">
                        <div class="card z-index-2 h-100">
                            <div class="card-header pb-0 pt-3 bg-transparent">
                                <h6 class="text-capitalize">Powers Chart</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="chart">
                                    <canvas id="chart-power" class="chart-canvas" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-lg-12 mb-lg-0 mb-4">
                        <div class="card z-index-2 h-100">
                            <div class="card-header pb-0 pt-3 bg-transparent">
                                <h6 class="text-capitalize">Voltages Chart</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="chart">
                                    <canvas id="chart-volt" class="chart-canvas" height="300"></canvas>
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
<script type="text/javascript" src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
<script>
    var dates = JSON.parse('{!! json_encode($dates) !!}');
    var volts = JSON.parse('{!! json_encode($volts) !!}');
    var currents = JSON.parse('{!! json_encode($currents) !!}');
    var freqs = JSON.parse('{!! json_encode($freqs) !!}');
    var p = JSON.parse('{!! json_encode($p) !!}');
    var q = JSON.parse('{!! json_encode($q) !!}');
    var s = JSON.parse('{!! json_encode($s) !!}');
    // console.log(predicts);

    var ctx = document.getElementById("chart-power").getContext("2d");
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    data: p,
                    label: "Active (kW)",
                    borderColor: "#F56565",
                    fill: false
                },
                {
                    data: q,
                    label: "Reactive (kVAr)",
                    borderColor: "#FBD38D",
                    fill: false
                },
                {
                    data: s,
                    label: "Apparent (kVA)",
                    borderColor: "#63B3ED",
                    fill: false
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                }
            },
            scales: {
                x: [{
                    type: 'time',
                    time: {
                        unit: 'minute'
                    },
                }],
                title: {
                    display: false,
                }
            }
        },
    })

    var ctx2 = document.getElementById("chart-volt").getContext("2d");
    var chart2 = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    data: volts,
                    label: "Voltage (V)",
                    borderColor: "#F56565",
                    fill: false
                },
                {
                    data: freqs,
                    label: "Freq (Hz)",
                    borderColor: "#FBD38D",
                    fill: false
                },
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                intersect: false,
                mode: 'index',
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                }
            },
            scales: {
                x: [{
                    title: {
                        display: true,
                        text: 'Timestamps'
                    },
                    type: 'time',
                    time: {
                        unit: 'minute'
                    },

                }],
                title: {
                    display: false,
                }
            }
        },
    })
</script>
@endpush