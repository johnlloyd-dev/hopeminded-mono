<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retake extends Model
{
    use HasFactory;
    protected $fillable = ['allowed_retake', 'student_id', 'attributes', 'flag', 'textbook_flag'];
}
