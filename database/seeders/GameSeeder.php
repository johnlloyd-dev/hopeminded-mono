<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Game::updateOrCreate([
            'name' => 'Hangman Game',
            'flag' => 'hangman-game',
            'perfect_score' => 18,
            'passing_score' => 10,
            'textbook_flag' => 'alphabet_words'
        ]);

        Game::updateOrCreate([
            'name' => 'Typing Balloon',
            'flag' => 'typing-balloon',
            'perfect_score' => 51,
            'passing_score' => 38,
            'textbook_flag' => 'vowel_consonants'
        ]);

        Game::updateOrCreate([
            'name' => 'Memory Game',
            'flag' => 'memory-game',
            'perfect_score' => 59, //Level 1: 5 (add 5)->Level 2: 23 (add 18)->Level 3: 59 (add 24)
            'passing_score' => 44,
            'textbook_flag' => 'alphabet_letters'
        ]);
    }
}
