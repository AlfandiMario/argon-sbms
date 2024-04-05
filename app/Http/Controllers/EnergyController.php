<?php

namespace App\Http\Controllers;

use App\Models\Energy;
use App\Models\EnergyKwh;
use App\Models\EnergyCost;
use App\Models\IkeStandar;
use Illuminate\Http\Request;
use App\Models\EnergyPredict;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EnergyController extends Controller
{
    public function monitor()
    {
        $title = 'Energy Monitoring';
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::now()->subDay()->toDateString();
        $thisMonth = Carbon::now()->month; // return int
        $lastMonth = Carbon::now()->month - 1;
        $twoMonthAgo = Carbon::now()->month - 2;

        $energies = Energy::where('id_kwh', '1')->latest()->first();
        $lastKwh = EnergyKwh::whereDate('created_at', $today)->orderByDesc('updated_at')->first()->total_energy / 1000;
        $yesterdayKwh = EnergyKwh::whereDate('created_at', $yesterday)->orderByDesc('updated_at')->first()->total_energy / 1000;
        $todayEnergy = $lastKwh - $yesterdayKwh;

        $twoMonthAgoKwh = EnergyKwh::whereMonth('created_at', $twoMonthAgo)->orderByDesc('updated_at')->first()->total_energy / 1000;
        $prevMonthKwh = EnergyKwh::whereMonth('created_at', $lastMonth)->orderByDesc('updated_at')->first()->total_energy / 1000 - $twoMonthAgoKwh;
        $thisMonthKwh = EnergyKwh::whereMonth('created_at', $thisMonth)->orderByDesc('updated_at')->first()->total_energy / 1000 - $prevMonthKwh;

        // dd($thisMonthKwh);

        $tarif = EnergyCost::latest()->pluck('harga')->first();
        $lastMonthCost = $prevMonthKwh  * $tarif;
        $thisMonthCost = $thisMonthKwh  * $tarif;


        $collection = ["Freq (Hz)", "Ampere (A)", "Voltage (V)", "Power (kWh)", "Reactive P (kVAR)", "Apparent P (kVA)"];
        $keys = ['frekuensi', 'arus', 'tegangan', 'active_power', 'reactive_power', 'apparent_power'];
        $collection2 = ["Today (kWh)", "This Month (kWh)", "Tariff (Rp)", "This Month Cost (Rp)", "Last Month Cost (Rp)"];
        $values2 = [$todayEnergy, $thisMonthKwh, $tarif, $thisMonthCost, $lastMonthCost];

        return view("pages.energy.monitor", compact('title', 'collection', 'keys', 'collection2', 'energies', 'values2'));
    }

    public function showControl()
    {
        $title = 'Energy Control';
        $devices = ["Main Lamp", "AC", "Second Lamp", "Air Purifier", "Fridge", "CCTV"];
        $status = [1, 0, 1, 0, 1, 0];

        return view("pages.energy.control", compact('devices', 'status', 'title'));
    }

    public function stats()
    {
        $title = 'Energy Statistic';
        $collection = $this->getDailyEnergyReversed();
        $dailyEnergy = [];
        $dates = [];
        foreach ($collection as $item) {
            array_push($dailyEnergy, $item->today_energy);
            $date = Carbon::parse($item->date)->format('M j');
            array_push($dates, $date);
        }

        return view("pages.energy.stats", compact('title', 'dates', 'dailyEnergy'));
    }

    public function standarIke()
    {
        $collection = ["Yesterday", "This Month", "Tariff", "This Month Cost", "Last Month Cost"];
        $value = ["4 kWh", "15 kWh", "1440 IDR", "120k", "300k"];

        return view("pages.ike.index", compact('collection', 'value'));
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

    public function getMonthlyEnergy()
    {
        // Versi Mario
        $data = EnergyKwh::selectRaw('MONTH(created_at) as month, YEAR(created_at) as tahun, MAX(created_at) as latest_updated, MAX(total_energy) as total_energy')
            ->where('id_kwh', '=', '1')
            ->groupBy('month', 'tahun')
            ->latest('latest_updated')
            ->get();

        $price = EnergyCost::latest()->first()->pokok;

        $length = count($data);
        // $data[$length-1]->monthly_kwh = ($data[$length-1]->energy_meter - 6950)/1000; // pertama kali pasang di 30 des dengan kwh meter start dari 6950

        for ($i = 0; $i < $length - 1; $i++) {
            $data[$i]->monthly_kwh = ($data[$i]->energy_meter - $data[$i + 1]->energy_meter) / 1000; // energy perbulan dalam kWh
            $data[$i]->bill = intval($data[$i]->monthly_kwh * $price); // biaya listrik perbulan
            $angka_ike = $data[$i]->monthly_kwh / 33.1;
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

        $data->makeHidden(['energy_meter']);

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
}