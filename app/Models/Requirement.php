<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = ['procurement_id', 'requirement', 'date_submitted', 'date_returned', 'is_checked'];

    public function procurement()
    {
        return $this->belongsTo(Procurement::class);
    }
}