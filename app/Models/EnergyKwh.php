<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EnergyKwh extends Model
{
    use HasFactory;
    protected $table = 'energy_kwh';
    protected $fillable = ['id_kwh', 'total_energy'];
}
