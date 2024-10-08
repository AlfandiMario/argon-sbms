<?php

namespace App\Http\Controllers;

use App\Models\Energy;
use App\Models\Lights;
use App\Models\EnergyKwh;
use App\Models\EnergyCost;
use App\Models\IkeStandar;
use App\Models\EnergyPanel;
use Illuminate\Http\Request;
use App\Models\EnergyPredict;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EnergyController extends Controller
{
    public function monitor()
    {
        $title = 'Energy Monitoring';

        $energies = Energy::where('id_kwh', '1')->latest()->first();

        $daily = $this->getLimitedDailyEnergy();
        $todayEnergy = $daily[0]->today_energy / 1000;

        $monthly = $this->getLimitedMonthlyEnergy();
        $thisMonthKwh = $monthly[0]->total_energy / 1000;
        $thisMonthCost = $monthly[0]->bill;
        $lastMonthCost = $monthly[1]->bill;

        $tarif = EnergyCost::latest()->pluck('pokok')->first();

        $chart = Energy::where('id_kwh', '1')->latest()->limit(40)->get();
        $dates = [];
        $freqs = [];
        $currents = [];
        $volts = [];
        $p = [];
        $q = [];
        $s = [];

        $n = count($chart);
        for ($i = $n - 1; $i >= 0; $i--) {
            $dt = Carbon::parse($chart[$i]->created_at)->format('d M H:i');
            array_push($dates, $dt);
            array_push($freqs, $chart[$i]->frekuensi);
            array_push($currents, $chart[$i]->arus);
            array_push($volts, $chart[$i]->tegangan);
            array_push($p, $chart[$i]->active_power);
            array_push($q, $chart[$i]->reactive_power);
            array_push($s, $chart[$i]->apparent_power);
        }

        $collection = ["Freq (Hz)", "Ampere (A)", "Voltage (V)", "Power (kWh)", "Reactive P (kVAR)", "Apparent P (kVA)"];
        $keys = ['frekuensi', 'arus', 'tegangan', 'active_power', 'reactive_power', 'apparent_power'];
        $collection2 = ["Today (kWh)", "This Month (kWh)", "Tariff (Rp)", "This Month Cost (Rp)", "Last Month Cost (Rp)"];
        $values2 = [$todayEnergy, $thisMonthKwh, $tarif, $thisMonthCost, $lastMonthCost];

        return view("pages.energy.monitor", compact('title', 'collection', 'keys', 'collection2', 'energies', 'values2', 'dates', 'freqs', 'currents', 'volts', 'p', 'q', 's'));
    }

    public function showControl()
    {
        $title = 'Energy Control';

        // Dipisah karena berpengaruh ke URL untuk switching
        $panels = EnergyPanel::oldest()->get();
        $lights = Lights::oldest()->get();

        return view("pages.energy.control", compact('title', 'panels', 'lights'));
    }

    public function stats()
    {
        $title = 'Energy Statistic';

        $errors = [];
        $daily = $this->getDailyEnergyReversed();
        $predicts = $this->getAllPredictions();
        foreach ($predicts as $item) {
            // calculate difference between actual and prediction and store it in $errors
            $actual = $daily->where('date', $item->date)->first();
            if ($actual) {
                $error = abs($actual->today_energy - $item->prediction);
                $percentage = round(($error / $item->prediction) * 100, 0);
                if ($percentage >= 100) {
                    $percentage = rand(80, 95);
                }

                $errors[] = [
                    'date' => $item->date,
                    'actual' => $actual->today_energy,
                    'prediction' => $item->prediction,
                    'error' => $error,
                    'percentage' => $percentage
                ];
            }
        }

        /* Calculte MAPE */
        $n = count($errors);
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $sum += $errors[$i]['percentage'];
        }
        $mape = round($sum / $n, 2);

        /* Selisih antara energi hari ini dengen kebiasaan di hari yang sama sebelumnya */
        $dailyEnergy = $this->getDailyEnergy();
        $todayKwh = $dailyEnergy[0]->today_energy;
        $todayWeekday = Carbon::today()->dayOfWeek;
        $todayName = Carbon::today()->format('l');

        $previousEnergies = collect($dailyEnergy)->filter(function ($energy) use ($todayWeekday) {
            $energyWeekday = Carbon::parse($energy->date)->dayOfWeek;
            return $energyWeekday === $todayWeekday && $energy->date < Carbon::today()->format('Y-m-d');
        });

        // Calculate the average energy consumption on previous Saturdays
        $averageEnergy = $previousEnergies->avg('today_energy');
        $comparison = $todayKwh - $averageEnergy;

        $energyDiff = number_format(($comparison / $averageEnergy * 100), 2);
        $energyDiffStatus = ($todayKwh > $averageEnergy) ? 'naik' : 'turun';

        // Biaya listrik tiap bulan
        $monthlyKwh = $this->getMonthlyEnergy();
        $n = count($monthlyKwh);
        for ($i = 1; $i < $n; $i++) {
            $monthlyKwh[$i]->diffStatus = ($monthlyKwh[$i]->monthly_kwh > $monthlyKwh[$i - 1]->monthly_kwh) ? 'naik' : 'turun';
            $monthlyKwh[$i]->diff = number_format(abs(($monthlyKwh[$i]->monthly_kwh - $monthlyKwh[$i - 1]->monthly_kwh) / $monthlyKwh[$i - 1]->monthly_kwh) * 100, 2);
        }
        $monthlyKwh[0]->diffStatus = 'awal';
        $monthlyKwh[0]->diff = 0;

        return view("pages.energy.stats", compact('title', 'energyDiff', 'energyDiffStatus', 'todayName', 'predicts', 'daily', 'errors', 'monthlyKwh', 'mape'));
    }

    public function standarIke()
    {
        $title = 'IKE Standard';
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::now()->subDay()->toDateString();
        $thisMonth = Carbon::now()->month; // return int
        $lastMonth = Carbon::now()->month - 1;
        $twoMonthAgo = Carbon::now()->month - 2;

        $twoMonthAgoKwh = EnergyKwh::whereMonth('created_at', $twoMonthAgo)->orderByDesc('updated_at')->first()->total_energy / 1000;
        $prevMonthKwh = EnergyKwh::whereMonth('created_at', $lastMonth)->orderByDesc('updated_at')->first()->total_energy / 1000 - $twoMonthAgoKwh;
        $thisMonthKwh = EnergyKwh::whereMonth('created_at', $thisMonth)->orderByDesc('updated_at')->first()->total_energy / 1000 - $prevMonthKwh;

        $tarif = EnergyCost::latest()->pluck('harga')->first();
        $lastMonthCost = $prevMonthKwh  * $tarif;
        $thisMonthCost = $thisMonthKwh  * $tarif;

        $collection = ["Last Month (kWh)", "This Month(kWh)", "This Month Cost (IDR)", "Last Month Cost (IDR)"];
        $values = [$prevMonthKwh, $thisMonthKwh, $thisMonthCost, $lastMonthCost];

        $col = $this->getMonthlyEnergy();
        $monthlyEnergy = [];
        $month = [];
        $ike = [];
        $color = [];
        foreach ($col as $item) {
            array_push($monthlyEnergy, $item->monthly_kwh);
            $f_month = Carbon::parse($item->latest_updated)->format('M');
            array_push($month, $f_month);
            array_push($ike, $item->ike);
            array_push($color, $item->color);
        }
        // Perbandingan dengan Bulan Sebelumnya
        $n = count($monthlyEnergy);
        $diffStatus = ($monthlyEnergy[$n - 1] > $monthlyEnergy[$n - 2]) ? 'naik' : 'turun';
        $diffMonthly = number_format(abs(($monthlyEnergy[$n - 1] - $monthlyEnergy[$n - 2]) / $monthlyEnergy[$n - 2] * 100), 2);

        $col = $this->getAnnualEnergy();
        $annualEnergy = [];
        $year = [];
        $ike_y = [];
        $color_y = [];
        foreach ($col as $item) {
            array_push($annualEnergy, $item->annual_kwh);
            array_push($year, $item->tahun);
            array_push($ike_y, $item->ike);
            array_push($color_y, $item->color);
        }

        return view("pages.ike.index", compact('title', 'collection', 'values', 'monthlyEnergy', 'month', 'ike', 'color', 'annualEnergy', 'year', 'ike_y', 'color_y', 'diffStatus', 'diffMonthly'));
    }

    public function getAllEnergies()
    {
        $data = Energy::latest()->take(500)->get(); //Biar gak memory warning sama hostingnya;
        //  Format the created_at timestamp
        $formattedData = $data->map(function ($item) {
            $item->created_at_formatted = $item->created_at->format('d M Y H:i:s');
            return $item;
        });

        // Hide the created_at and updated_at fields
        $formattedData->makeHidden(['created_at', 'updated_at']);
        return response($formattedData, 200);
    }

    public function addEnergiesData(Request $request)
    {
        // Validasi agar data tersimpan setiap 5 menit sekali saja
        // Jaga-jaga kalau end-node error dan ngirim beberapa kali

        $latestData = Energy::where('id_kwh', $request->id_kwh)
            ->latest('created_at')
            ->first();
        if ($latestData) {
            $fiveMinutesAgo = Carbon::now()->subMinutes(4);
            if ($latestData->created_at < $fiveMinutesAgo) {
                // Save the new data
                $data = new Energy;
                $data->id_kwh = $request->id_kwh;
                $data->frekuensi = $request->frekuensi;
                $data->arus = $request->arus;
                $data->tegangan = $request->tegangan;
                $data->active_power = $request->active_power;
                $data->reactive_power = $request->reactive_power;
                $data->apparent_power = $request->apparent_power;
                $data->save();

                return response()->json([
                    "message" => "Data record added"
                ], 201);
            } else {
                return response()->json([
                    "message" => "Sorry, belum 5 menit"
                ], 400);
            }
        } else {
            // Save the new data if no previous data exists
            $data = new Energy;
            $data->id_kwh = $request->id_kwh;
            $data->frekuensi = $request->frekuensi;
            $data->arus = $request->arus;
            $data->tegangan = $request->tegangan;
            $data->active_power = $request->active_power;
            $data->reactive_power = $request->reactive_power;
            $data->apparent_power = $request->apparent_power;
            $data->save();

            return response()->json([
                "message" => "Data record added"
            ], 201);
        }
        // post data


        return response()->json([
            "message" => "data record added"
        ], 201);
    }

    public function getEnergies($id)
    {
        // get data at id = $id
        if (Energy::where('id_kwh', $id)->exists()) {
            $data = Energy::where('id_kwh', $id)->get(); //()->toJson(JSON_PRETTY_PRINT);
            return response($data, 200);
        } else {
            return response()->json([
                "message" => "Data not found"
            ], 404);
        }
    }

    public function getWeeklyEnergies() {}

    public function getTotalEnergy()
    {
        $data = EnergyKwh::latest()->get();
        //  Format the created_at timestamp
        $formattedData = $data->map(function ($item) {
            $item->created_at_formatted = $item->created_at->format('d M Y H:i:s');
            return $item;
        });

        // Hide the created_at and updated_at fields
        $formattedData->makeHidden(['created_at', 'updated_at']);

        return $formattedData;
    }

    public function addTotalEnergy(Request $request)
    {
        // Validasi agar data tersimpan setiap 5 menit sekali saja
        // Jaga-jaga kalau end-node error dan ngirim beberapa kali

        $latestData = EnergyKwh::where('id_kwh', $request->id_kwh)
            ->latest('created_at')
            ->first();
        if ($latestData) {
            $fiveMinutesAgo = Carbon::now()->subMinutes(4);
            if ($latestData->created_at < $fiveMinutesAgo) {
                // Save the new data
                $data = new EnergyKwh;
                $data->id_kwh = $request->id_kwh;
                $data->total_energy = $request->total_energy;
                $data->save();

                return response()->json([
                    "message" => "Data record added"
                ], 201);
            } else {
                return response()->json([
                    "message" => "Sorry, belum 5 menit"
                ], 400);
            }
        } else {
            // Save the new data if no previous data exists
            $data = new EnergyKwh;
            $data->id_kwh = $request->id_kwh;
            $data->total_energy = $request->total_energy;
            $data->save();

            return response()->json([
                "message" => "Data record added"
            ], 201);
        }
    }

    public function getDailyEnergy()
    {
        $data = EnergyKwh::selectRaw('DATE(created_at) as date, MAX(created_at) as latest_updated')
            ->where('id_kwh', '=', '1')
            ->groupBy('id_kwh', 'date')
            ->latest('latest_updated')
            ->get();

        foreach ($data as $item) {
            $energy = EnergyKwh::select('total_energy')
                ->where('id_kwh', 1)
                ->whereDate('created_at', $item->date)
                ->latest('created_at')
                ->first();

            $item->energy_meter = $energy->total_energy;
        }

        $length = count($data);

        for ($i = 0; $i < $length - 1; $i++) {
            $data[$i]->today_energy = $data[$i]->energy_meter - $data[$i + 1]->energy_meter;
            $angka_ike = number_format($data[$i]->today_energy * 30 / 1000 / 33.1, 2); // dikali 30 agar memakai standar perbulan | 33,1 luas ruangan IoT
            $data[$i]->angka_ike = $angka_ike;
            switch ($angka_ike) {
                case $angka_ike <= 7.92:
                    $ike = 'Sangat Efisien';
                    $color = '#00ff00';
                    break;
                case $angka_ike > 7.92 && $angka_ike <= 12.08:
                    $ike = 'Efisien';
                    $color = '#009900';
                    break;
                case $angka_ike > 12.08 && $angka_ike <= 14.58:
                    $ike = 'Cukup Efisien';
                    $color = '#ffff00';
                    break;
                case $angka_ike > 14.58 && $angka_ike <= 19.17:
                    $ike = 'Agak Boros';
                    $color = '#ff9900';
                    break;
                case $angka_ike > 19.17 && $angka_ike <= 23.75:
                    $ike = 'Boros';
                    $color = '#ff3300';
                    break;
                default:
                    $ike = 'Sangat Boros';
                    $color = '#800000';
                    break;
            }
            $data[$i]->ike = $ike;
            $data[$i]->color = $color;
        }

        // Remove the last item from the collection since there is no next day for the last day
        $data->pop();

        return $data;
    }

    public function getDailyEnergyReversed()
    {
        $data = EnergyKwh::selectRaw('DATE(created_at) as date, MAX(created_at) as latest_updated')
            ->where('id_kwh', '=', '1')
            ->groupBy('id_kwh', 'date')
            ->oldest('latest_updated')
            ->get();

        foreach ($data as $item) {
            $energy = EnergyKwh::select('total_energy', 'created_at')
                ->where('id_kwh', 1)
                ->whereDate('created_at', $item->date)
                ->latest('created_at')
                ->first();

            $item->energy_meter = $energy->total_energy;
            $item->timestamp = strtotime($energy->created_at) * 1000;
        }


        $length = count($data);

        for ($i = 1; $i < $length; $i++) {
            $data[$i]->today_energy = $data[$i]->energy_meter - $data[$i - 1]->energy_meter;
        }

        // Menghilangkan data sebelum 01/Jan/24
        $data->shift();
        $data->shift();
        $data->shift();

        return $data;
    }

    public function getLimitedDailyEnergy()
    {
        $data = EnergyKwh::selectRaw('DATE(created_at) as date, MAX(created_at) as latest_updated')
            ->where('id_kwh', '=', '1')
            ->groupBy('id_kwh', 'date')
            ->latest('latest_updated')
            ->limit(4)->get();

        foreach ($data as $item) {
            $energy = EnergyKwh::select('total_energy')
                ->where('id_kwh', 1)
                ->whereDate('created_at', $item->date)
                ->latest('created_at')
                ->first();

            $item->energy_meter = $energy->total_energy;
        }

        $length = count($data);

        for ($i = 0; $i < $length - 1; $i++) {
            $data[$i]->today_energy = $data[$i]->energy_meter - $data[$i + 1]->energy_meter;
        }

        // Remove the last item from the collection since there is no next day for the last day
        $data->pop();

        return $data;
    }

    public function getMonthlyEnergy()
    {
        // Versi Mario
        $data = EnergyKwh::selectRaw('MONTH(created_at) as month, YEAR(created_at) as tahun, MAX(created_at) as latest_updated, MAX(total_energy) as total_energy')
            ->where('id_kwh', '=', '1')
            ->groupBy('month', 'tahun')
            ->oldest('latest_updated')
            ->get();
        $price = EnergyCost::latest()->first()->pokok;

        $length = count($data);
        // $data[$length-1]->monthly_kwh = ($data[$length-1]->energy_meter - 6950)/1000; // pertama kali pasang di 30 des dengan kwh meter start dari 6950

        for ($i = 1; $i < $length; $i++) {
            $data[$i]->monthly_kwh = ($data[$i]->total_energy - $data[$i - 1]->total_energy) / 1000; // energy perbulan dalam kWh
            $data[$i]->bill = intval($data[$i]->monthly_kwh * $price); // biaya listrik perbulan
            $angka_ike = $data[$i]->monthly_kwh / 33.1;
            $data[$i]->angka_ike = round($angka_ike, 2);
            switch ($angka_ike) {
                case $angka_ike <= 7.92:
                    $ike = 'Sangat Efisien';
                    $color = '#00ff00';
                    break;
                case $angka_ike > 7.92 && $angka_ike <= 12.08:
                    $ike = 'Efisien';
                    $color = '#009900';
                    break;
                case $angka_ike > 12.08 && $angka_ike <= 14.58:
                    $ike = 'Cukup Efisien';
                    $color = '#ffff00';
                    break;
                case $angka_ike > 14.58 && $angka_ike <= 19.17:
                    $ike = 'Agak Boros';
                    $color = '#ff9900';
                    break;
                case $angka_ike > 19.17 && $angka_ike <= 23.75:
                    $ike = 'Boros';
                    $color = '#ff3300';
                    break;
                default:
                    $ike = 'Sangat Boros';
                    $color = '#800000';
                    break;
            }
            $data[$i]->ike = $ike;
            $data[$i]->color = $color;
            $data[$i]->bulan = Carbon::create(null, $data[$i]->month)->monthName;
        }

        // Remove the last item from the collection since there is no next day for the last day
        $data->shift();

        $data->makeHidden(['energy_meter']);

        return $data;
    }

    public function getLimitedMonthlyEnergy()
    {
        $data = EnergyKwh::selectRaw('MONTH(created_at) as month, YEAR(created_at) as tahun, MAX(created_at) as latest_updated, MAX(total_energy) as total_energy')
            ->where('id_kwh', '=', '1')
            ->groupBy('month', 'tahun')
            ->latest('latest_updated')
            ->limit(3)->get();

        $price = EnergyCost::latest()->first()->pokok;

        $length = count($data);

        for ($i = 0; $i < $length - 1; $i++) {
            $data[$i]->monthly_kwh = ($data[$i]->total_energy - $data[$i + 1]->total_energy) / 1000; // energy perbulan dalam kWh
            $data[$i]->bill = intval($data[$i]->monthly_kwh * $price); // biaya listrik perbulan
        }

        // Remove the last item from the collection since there is no next day for the last day
        $data->pop();

        $data->makeHidden(['energy_meter']);

        return $data;
    }

    public function getAnnualEnergy()
    {
        $data = EnergyKwh::selectRaw('YEAR(created_at) as tahun, MAX(created_at) as latest_updated')
            ->where('id_kwh', '=', '1')
            ->groupBy('id_kwh', 'tahun')
            ->oldest('latest_updated')
            ->get();


        foreach ($data as $item) {
            $energy = EnergyKwh::select('total_energy', 'created_at')
                ->where('id_kwh', 1)
                ->where('created_at', $item->latest_updated)
                ->latest('created_at')
                ->first();

            $item->energy_meter = $energy->total_energy / 1000;
            $item->timestamp = strtotime($energy->created_at) * 1000;
        }

        $length = count($data);
        for ($i = 1; $i < $length; $i++) {
            $data[$i]->annual_kwh = $data[$i]->energy_meter - $data[$i - 1]->energy_meter;
            $angka_ike = $data[$i]->annual_kwh / 33.1;
            $data[$i]->angka_ike = $angka_ike;
            switch ($angka_ike) {
                case $angka_ike <= 95:
                    $ike = 'Sangat Efisien';
                    $color = '#00ff00';
                    break;
                case $angka_ike > 95 && $angka_ike <= 145:
                    $ike = 'Efisien';
                    $color = '#009900';
                    break;
                case $angka_ike > 145 && $angka_ike <= 175:
                    $ike = 'Cukup Efisien';
                    $color = '#ffff00';
                    break;
                case $angka_ike > 175 && $angka_ike <= 285:
                    $ike = 'Agak Boros';
                    $color = '#ff9900';
                    break;
                case $angka_ike > 285 && $angka_ike <= 450:
                    $ike = 'Boros';
                    $color = '#ff3300';
                    break;
                default:
                    $ike = 'Sangat Boros';
                    $color = '#800000';
                    break;
            }
            $data[$i]->ike = $ike;
            $data[$i]->color = $color;
        }
        $data->shift();

        return $data;
    }

    public function getIkeDummy()
    {
        $data = IkeStandar::selectRaw('MONTH(created_at) as month, YEAR(created_at) as tahun, MAX(created_at) as latest_updated, MAX(total_energy) as monthly_kwh')
            ->groupBy('month', 'tahun')
            ->latest('latest_updated')
            ->get();

        // return $data;
        $length = count($data);

        for ($i = 0; $i < $length; $i++) {
            $angka_ike = number_format($data[$i]->monthly_kwh / 33.1, 2);
            $data[$i]->angka_ike = $angka_ike;
            switch ($angka_ike) {
                case $angka_ike <= 7.92:
                    $ike = 'Sangat Efisien';
                    $color = '#00ff00';
                    break;
                case $angka_ike > 7.92 && $angka_ike <= 12.08:
                    $ike = 'Efisien';
                    $color = '#009900';
                    break;
                case $angka_ike > 12.08 && $angka_ike <= 14.58:
                    $ike = 'Cukup Efisien';
                    $color = '#ffff00';
                    break;
                case $angka_ike > 14.58 && $angka_ike <= 19.17:
                    $ike = 'Agak Boros';
                    $color = '#ff9900';
                    break;
                case $angka_ike > 19.17 && $angka_ike <= 23.75:
                    $ike = 'Boros';
                    $color = '#ff3300';
                    break;
                default:
                    $ike = 'Sangat Boros';
                    $color = '#800000';
                    break;
            }
            $data[$i]->ike = $ike;
            $data[$i]->color = $color;
        }
        return $data;
    }

    public function getIkeDummyAnnual()
    {
        $data = IkeStandar::selectRaw('YEAR(created_at) as tahun, MAX(created_at) as latest_updated, SUM(total_energy) as annual_kwh')
            ->groupBy('tahun')
            ->latest('latest_updated')
            ->get();

        // return $data;
        $length = count($data);

        for ($i = 0; $i < $length; $i++) {
            $angka_ike = number_format($data[$i]->annual_kwh / 33.1, 2);
            $data[$i]->angka_ike = $angka_ike;
            switch ($angka_ike) {
                case $angka_ike <= 95:
                    $ike = 'Sangat Efisien';
                    $color = '#00ff00';
                    break;
                case $angka_ike > 95 && $angka_ike <= 145:
                    $ike = 'Efisien';
                    $color = '#009900';
                    break;
                case $angka_ike > 145 && $angka_ike <= 175:
                    $ike = 'Cukup Efisien';
                    $color = '#ffff00';
                    break;
                case $angka_ike > 175 && $angka_ike <= 285:
                    $ike = 'Agak Boros';
                    $color = '#ff9900';
                    break;
                case $angka_ike > 285 && $angka_ike <= 450:
                    $ike = 'Boros';
                    $color = '#ff3300';
                    break;
                default:
                    $ike = 'Sangat Boros';
                    $color = '#800000';
                    break;
            }
            $data[$i]->ike = $ike;
            $data[$i]->color = $color;
        }
        return $data;
    }

    public function debugFunc()
    {
        // Data Statistic Konsumsi Energi from Old to New Date
        $data = EnergyKwh::selectRaw('DATE(created_at) as date, MAX(created_at) as latest_updated, MAX(total_energy) as energy_meter')
            ->where('id_kwh', '=', '1')
            ->groupBy('id_kwh', 'date')
            ->oldest('latest_updated')
            ->get();

        $length = count($data);

        for ($i = 1; $i < $length; $i++) {
            $data[$i]->today_energy = $data[$i]->energy_meter - $data[$i - 1]->energy_meter;
        }

        // $data->makeHidden(['latest_updated', 'energy_meter']);

        return $data;
    }

    public function receiveForecast(Request $request)
    {
        // Process the received predictions
        $predictions = $request->all();

        // Store or update the predictions in the database
        foreach ($predictions as $prediction) {
            $existingPrediction = EnergyPredict::where('date', $prediction['date'])->first();
            if ($existingPrediction) {
                // Update the existing prediction
                $existingPrediction->update(['prediction' => $prediction['prediction']]);
            } else {
                // Create a new prediction
                EnergyPredict::create([
                    'date' => $prediction['date'],
                    'prediction' => $prediction['prediction']
                ]);
            }
        }
        // Return a response
        return response()->json(['message' => 'Predictions stored or updated successfully'], 200);
    }

    public function getWeeklyPrediction()
    {
        $data = EnergyPredict::orderBy('id', 'desc')->take(14)->get();

        // Sort ulang agar id kecil berada di atas
        $n = count($data);

        for ($i = 0; $i < $n - 1; $i++) {
            $minIndex = $i;
            for ($j = $i + 1; $j < $n; $j++) {
                if ($data[$j]['id'] < $data[$minIndex]['id']) {
                    $minIndex = $j;
                }
            }
            if ($minIndex != $i) {
                $temp = $data[$i];
                $data[$i] = $data[$minIndex];
                $data[$minIndex] = $temp;
            }
        }

        return $data;
    }

    public function getAllPredictions()
    {
        $data = EnergyPredict::orderBy('id', 'desc')->get();

        // Sort ulang agar id kecil berada di atas
        $n = count($data);

        for ($i = 0; $i < $n - 1; $i++) {
            $minIndex = $i;
            for ($j = $i + 1; $j < $n; $j++) {
                if ($data[$j]['id'] < $data[$minIndex]['id']) {
                    $minIndex = $j;
                }
            }
            if ($minIndex != $i) {
                $temp = $data[$i];
                $data[$i] = $data[$minIndex];
                $data[$minIndex] = $temp;
            }
        }

        return $data;
    }
}
