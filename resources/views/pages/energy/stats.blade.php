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
                                    <div class="row">
                                        <p class="text-sm">
                                            Average Forecast vs Actual Error :
                                            <span class="text-dark text-bolder">
                                                {{ $mape }} %
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="chart">
                                        <div id="chart-daily" style="width: 100%; height: 300px;"></div>
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
<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.2/dist/echarts.min.js"></script>
<script>
    var chart = echarts.init(document.getElementById('chart-daily'));

    var actualData = @json($daily -> map(function ($item) {
        return ['date' => $item['date'], 'value' => $item['today_energy']];
    }));
    var predictData = @json($predicts -> map(function ($item) {
        return ['date' => $item['date'], 'value' => $item['prediction']];
    }));

    var allDates = [...new Set([...actualData.map(d => d.date), ...predictData.map(d => d.date)])].sort();

    var options = {
        tooltip: {
            trigger: 'axis',
            formatter: function (params) {
                var date = params[0].axisValue;
                var error = @json($errors).find(e => e.date === date);
                return date + '<br/>' +
                    params.map(p => p.seriesName + ': ' + p.value[1]).join('<br/>') +
                    (error ? '<br/>Error: ' + error.error + '<br/>Percentage: ' + error.percentage + '%' : '');
            }
        },
        legend: {
            data: ['Actual', 'Forecasted']
        },
        xAxis: {
            type: 'category',
            data: allDates,
            axisLabel: {
                formatter: function (value) {
                    return new Date(value).toLocaleDateString('en-US', { day: '2-digit', month: 'short' });
                }
            }
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name: 'Actual',
                type: 'line',
                data: actualData.map(d => [d.date, d.value])
            },
            {
                name: 'Forecasted',
                type: 'line',
                data: predictData.map(d => [d.date, d.value])
            }
        ]
    };

    chart.setOption(options);
</script>

<script>
    document.getElementById('reModelButton').addEventListener('click', function () {
        const reModelButton = this;
        const predictButton = document.getElementById('predictButton');

        reModelButton.disabled = true;
        predictButton.disabled = true;

        fetch('https://iotlabforecast-907500994389.asia-southeast2.run.app/modelling')
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

        fetch('https://iotlabforecast-907500994389.asia-southeast2.run.app/predict')
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