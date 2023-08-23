<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizMistake extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'quiz_report_id', 'game_flag', 'attributes'];
}
