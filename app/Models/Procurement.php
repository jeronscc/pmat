<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Procurement extends Model
{
    use HasFactory;

    protected $fillable = ['pr_number', 'activity', 'status'];

    public function requirements()
    {
        return $this->hasMany(Requirement::class);
    }
}