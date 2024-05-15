<?php

namespace App\Http\Controllers;

use App\Models\DhtSensor;
use App\Models\EnergyPanel;
use App\Models\Lights;
use Illuminate\Http\Request;

class EnvironmentController extends Controller
{
    public function monitor()
    {
        $title = 'Environment Sensing';
        $collection = ["Temperature", "Humidity", "Light Intensity"];
        $value = ["25 C", "60 RH", "1000 Lux"];

        return view("pages.envi.sense", compact('title', 'collection', 'value'));
    }

    public function getDht()
    {
        $data = DhtSensor::latest()->get();
        return $data;
    }

    public function postDht(Request $request)
    {
        $data = new DhtSensor;
        $data->temperature = $request->temperature;
        $data->humidity = $request->humidity;
        $data->save();
        return 201;
    }

    public function getDeviceStatus()
    {
        $panels = EnergyPanel::oldest()->get();
        $lights = Lights::oldest()->get();

        $output = $panels->concat($lights);
        return $output;
    }

    public function switchPanel($id)
    {
        $panel = EnergyPanel::find($id);
        $panel->status = !$panel->status;
        $panel->save();
        return redirect()->back();
    }

    public function switchLight($id)
    {
        $light = Lights::find($id);
        $light->status = !$light->status;
        $light->save();
        return redirect()->back();
    }
}
