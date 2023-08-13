<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerfectScore extends Model
{
    use HasFactory;

    protected $fillable = ['score', 'flag', 'teacher_id'];
}
