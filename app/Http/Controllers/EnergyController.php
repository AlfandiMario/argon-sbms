<?php

namespace App\Http\Controllers;

use App\Models\Energy;
use App\Models\EnergyKwh;
use App\Models\EnergyCost;
use Illuminate\Http\Request;
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
        $devices = ["Main Lamp", "AC", "Second Lamp", "Air Purifier", "Fridge", "CCTV"];
        $status = [1, 0, 1, 0, 1, 0];

        return view("pages.energy.stats", compact('devices', 'status', 'title'));
    }

    public function standarIke()
    {
        $collection = ["Yesterday", "This Month", "Tariff", "This Month Cost", "Last Month Cost"];
        $value = ["4 kWh", "15 kWh", "1440 IDR", "120k", "300k"];

        return view("pages.ike.index", compact('collection', 'value'));
    }
}
