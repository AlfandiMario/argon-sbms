<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Energy extends Model
{
    use HasFactory;
    protected $fillable = ['id_kwh','frekuensi','arus','tegangan','active_power','reactive_power','apparent_power'];
}
