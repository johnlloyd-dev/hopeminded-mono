<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'email',
        'is_registered',
        'teacher_id',
        'user_id',
        'unhashed',
        'section_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function quiz()
    {
        return $this->hasMany(QuizReport::class, 'student_id', 'id')
            ->selectRaw('MAX(total_score) as highest_score, game_id, student_id')
            ->where('flag', 'quiz')
            ->where('mark', 'passed')
            ->groupBy('game_id', 'student_id')
            ->orderBy('game_id', 'ASC');
    }

    public function skill_test()
    {
        return $this->hasMany(SkillTest::class, 'student_id', 'id')
            ->selectRaw('MAX(score) as highest_score, letter, student_id, flag')
            ->where('status', 'correct')
            ->groupBy('flag', 'letter', 'student_id')
            ->orderBy('letter', 'ASC');
    }
}
