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
                    <div class="row">
                        <div class="col-lg-12 mb-lg-0 mb-4">
                            <div class="card z-index-2 h-100">
                                <div class="card-header pb-0 pt-3 bg-transparent">
                                    <h6 class="text-capitalize">Energy Consumption (Daily)</h6>
                                    <div class="row">
                                        <div class="col-sm">
                                            <p class="text-sm mb-0">
                                                @if($energyDiffStatus == 'naik')
                                                <i class="fa fa-arrow-up text-success"></i>
                                                <span class="font-weight-bold">{{ $energyDiff }}% more</span> than
                                                average in the previous {{ $todayName }}
                                                @else
                                                <i class="fa fa-arrow-down text-danger"></i>
                                                <span class="font-weight-bold">{{ $energyDiff }}% less</span> than
                                                average in the previous {{ $todayName }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-sm d-flex justify-content-end">
                                            <button id="reModelButton"
                                                class="btn btn-outline-danger btn-sm mx-2 my-0">Re-Modelling</button>
                                            <button id="predictButton"
                                                class="btn btn-outline-dark btn-sm my-0">Predict</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="chart">
                                        <canvas id="chart-daily" class="chart-canvas" height="300"></canvas>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-12 mb-lg-0 mb-4">
                            <div class="card z-index-2 h-100">
                                <div class="card-header pb-0 pt-3 bg-transparent">
                                    <h6 class="text-capitalize">Electricity Bill</h6>
                                </div>
                                <div class="card-body p-3">
                                    <table class="table table-striped table-hover">
                                        <tr>
                                            <th class="text-center" width="15%">Bulan</th>
                                            <th class="text-center" width="20%">Tahun</th>
                                            <th class="text-center" width="20%">Energi (KWH)</th>
                                            <th class="text-center" width="10%"></th>
                                            <th class="text-center" width="20%">Total</th>
                                            <th class="text-center" width="15%">Than Last Month</th>
                                        </tr>
                                        @foreach ($monthlyKwh as $item)
                                        <tr>
                                            <td class="text-start">{{$item->bulan}}</td>
                                            <td class="text-center">{{$item->tahun}}</td>
                                            <td class="text-center">@php echo
                                                number_format((float)$item->monthly_kwh,2,',',''); @endphp</td>
                                            <td class="text-end">Rp </td>
                                            <td class="text-end">@php echo
                                                number_format((float)$item->bill,'0',',','.'); @endphp</td>
                                            @if($item->diffStatus=='naik')
                                            <td class="text-center text-sm mb-0"><i
                                                    class="fa-solid fa-sort-up text-danger "></i><span class="mx-2">+
                                                    {{$item->diff }} %</span></td>
                                            @elseif ($item->diffStatus=='turun')
                                            <td class="text-center text-sm my-0 mx-2"><i
                                                    class="fa-solid fa-sort-down text-success "></i>
                                                <span class="mx-2">- {{$item->diff }} %</span>
                                            </td>
                                            @else()
                                            <td class="text-center text-sm mb-0"></td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </table>
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
    var daily = JSON.parse('{!! json_encode($daily) !!}');
    var predicts = JSON.parse('{!! json_encode($predicts) !!}');

    var ctx = document.getElementById("chart-daily").getContext("2d");
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                data: daily,
                label: "Energy",
                borderColor: "#63B3ED",
                fill: false
            },
            {
                data: predicts,
                label: "Prediction",
                borderColor: "rgba(108, 117, 125, 0.7)",
                fill: false
            }
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
                        unit: 'day'
                    },
                    ticks: {
                        display: true,
                        color: 'orange',
                        font: {
                            size: 11,
                            family: "Open Sans",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                }],
                y: {
                    title: {
                        display: true,
                        text: 'Energy (kWh)'
                    }
                },
                title: {
                    display: false,
                }
            }
        },
    });

    document.getElementById('reModelButton').addEventListener('click', function () {
        const reModelButton = this;
        const predictButton = document.getElementById('predictButton');

        reModelButton.disabled = true;
        predictButton.disabled = true;

        fetch('https://energyforecastlstm-k5gkihkf7q-et.a.run.app/modelling')
            .then(response => {
                if (response.ok) {
                    reModelButton.disabled = false;
                    predictButton.disabled = false;
                } else {
                    // Handle error case
                    reModelButton.disabled = false;
                    predictButton.disabled = false;
                    console.error('Error with remodeling request.');
                }
            })
            .catch(error => {
                reModelButton.disabled = false;
                predictButton.disabled = false;
                console.error('Fetch error: ', error);
            });
    });

    document.getElementById('predictButton').addEventListener('click', function () {
        const predictButton = this;
        const reModelButton = document.getElementById('reModelButton');

        predictButton.disabled = true;
        reModelButton.disabled = true;

        fetch('https://energyforecastlstm-k5gkihkf7q-et.a.run.app/predict')
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    // Handle error case
                    predictButton.disabled = false;
                    reModelButton.disabled = false;
                    console.error('Error with prediction request.');
                }
            })
            .catch(error => {
                predictButton.disabled = false;
                reModelButton.disabled = false;
                console.error('Fetch error: ', error);
            });
    });


</script>
@endpush