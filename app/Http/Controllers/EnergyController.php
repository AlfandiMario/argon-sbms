<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnergyController extends Controller
{
    // public function index()
    // {
    //     return view('pages.energy.energy-monitor');
    // }

    public function monitor()
    {
        $title = 'Energy Monitoring';
        $collection = ["Voltage", "Ampere", "Frequency", "Power", "Reactive Power", "Apparent Power"];
        $value = ["220 V", "1 A", "60 Hz", "300 W", "300 W", "300 W", "60 Hz"];

        $collection2 = ["Yesterday", "This Month", "Tariff", "This Month Cost", "Last Month Cost"];
        $value2 = ["4 kWh", "15 kWh", "1440 IDR", "120k", "300k"];

        return view("pages.energy.monitor", compact('title', 'collection', 'value', 'collection2', 'value2'));
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
