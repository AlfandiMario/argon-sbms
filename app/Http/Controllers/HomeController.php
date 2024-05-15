<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Energy;
use App\Models\DhtSensor;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function index()
    {
        $latestEnergies = Energy::where('id_kwh', '1')->latest()->first();
        $nowVolt = $latestEnergies->tegangan;
        $nowAmp = $latestEnergies->arus;
        $updatedEnergies = Carbon::parse($latestEnergies->updated_at)->format('d M y H:i');

        $latestEnvi = DhtSensor::latest()->first();
        $nowTemp = $latestEnvi->temperature;
        $nowHumid = $latestEnvi->humidity;
        $updatedEnvi = Carbon::parse($latestEnvi->updated_at)->format('d M y H:i');

        $energyController = new EnergyController();
        $col = $energyController->getMonthlyEnergy();
        $monthlyEnergy = [];
        $month = [];
        foreach ($col as $item) {
            array_push($monthlyEnergy, $item->monthly_kwh);
            $f_month = Carbon::parse($item->latest_updated)->format('M');
            array_push($month, $f_month);
        }
        $n = count($monthlyEnergy);
        $diffStatus = ($monthlyEnergy[$n - 1] > $monthlyEnergy[$n - 2]) ? 'naik' : 'turun';
        $diffMonthly = number_format(abs(($monthlyEnergy[$n - 1] - $monthlyEnergy[$n - 2]) / $monthlyEnergy[$n - 2] * 100), 2);

        $envicon = new EnvironmentController();
        $deviceStatus = $envicon->getDeviceStatus();
        // dd($deviceStatus);

        return view('pages.dashboard', compact('nowVolt', 'nowAmp', 'updatedEnergies', 'nowTemp', 'nowHumid', 'updatedEnvi', 'monthlyEnergy', 'month', 'diffStatus', 'diffMonthly', 'deviceStatus'));
    }
}
