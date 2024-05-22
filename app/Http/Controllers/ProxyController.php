<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProxyController extends Controller
{
    public function predict()
    {
        $response = Http::get('https://energyforecastlstm-k5gkihkf7q-et.a.run.app/predict');
        return $response->body();
    }
    public function remodel()
    {
        $response = Http::get('https://energyforecastlstm-k5gkihkf7q-et.a.run.app/modelling');
        return $response->body();
    }
}
