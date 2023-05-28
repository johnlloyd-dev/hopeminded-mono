<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlagAttribute extends Model
{
    use HasFactory;
    protected $fillables = ['teacher_id', 'flag', 'attributes'];
}
