<?php

use App\Http\Controllers\EnergyController;
use App\Http\Controllers\EnvironmentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Energy Stuff
Route::get('ApiEnergy', [EnergyController::class, 'getAllEnergies']);
Route::post('ApiEnergy', [EnergyController::class, 'addEnergiesData']);
Route::get('ApiEnergy/{id}', [EnergyController::class, 'getEnergies']);
Route::get('debug-func', [EnergyController::class, 'debugFunc']);
Route::get('total-energy', [EnergyController::class, 'getTotalEnergy']);
Route::post('total-energy', [EnergyController::class, 'addTotalEnergy']);
Route::get('daily-energy', [EnergyController::class, 'getDailyEnergy']);
Route::get('daily-energy-reversed', [EnergyController::class, 'getDailyEnergyReversed']);
Route::get('monthly-energy', [EnergyController::class, 'getMonthlyEnergy']);
Route::get('annual-energy', [EnergyController::class, 'getAnnualEnergy']);
Route::get('ike-dummy', [EnergyController::class, 'getIkeDummy']);
Route::get('ike-dummy-annual', [EnergyController::class, 'getIkeDummyAnnual']);
Route::post('receive-forecast', [EnergyController::class, 'receiveForecast']);
Route::get('weekly-prediction', [EnergyController::class, 'getWeeklyPrediction']);

// Environment Stuff
Route::get('dht', [EnvironmentController::class, 'getDHT']);
Route::post('dht', [EnvironmentController::class, 'postDHT']);

// Device Stuff
Route::get('device-status', [EnvironmentController::class, 'getDeviceStatus']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
