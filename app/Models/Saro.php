<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saro extends Model
{
    use HasFactory;

    protected $table = 'saro';

    protected $fillable = [
        'saro_number',
        'budget',
        'year',
    ];
}
