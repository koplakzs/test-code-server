<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = ["name", "count", "province", "district", "subDistrict", "date", "proof", "note", "reason", "status"];
}
