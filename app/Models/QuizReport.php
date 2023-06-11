<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizReport extends Model
{
    use HasFactory;
    protected $fillable = ['student_id', 'game_id', 'highest_level', 'total_score', 'mark', 'flag'];
}
