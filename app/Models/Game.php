<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'flag', 'textbook_flag'];

    public function tutorialScore()
    {
        return $this->hasMany(GameScore::class)->where('type', 'tutorial');
    }

    public function quizScore()
    {
        return $this->hasMany(GameScore::class)->where('type', 'quiz');
    }
}
