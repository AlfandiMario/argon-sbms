<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnergyController extends Controller
{
    // public function index()
    // {
    //     return view('pages.energy.energy-monitor');
    // }

    public function index(string $page)
    {
        if (view()->exists("pages.energy.{$page}")) {
            return view("pages.energy.{$page}");
        }

        return abort(404);
    }
}
