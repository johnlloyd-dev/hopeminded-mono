<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\GameScore;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Game Seeder
        $hangmanGame = Game::updateOrCreate([
            'name' => 'Hangman Game',
            'flag' => 'hangman-game',
            'textbook_flag' => 'alphabet_words'
        ]);

        $typingBalloon = Game::updateOrCreate([
            'name' => 'Typing Balloon',
            'flag' => 'typing-balloon',
            'textbook_flag' => 'vowel_consonants'
        ]);

        $memoryGame = Game::updateOrCreate([
            'name' => 'Memory Game',
            'flag' => 'memory-game',
            'textbook_flag' => 'alphabet_letters'
        ]);

        $matchingGame = Game::updateOrCreate([
            'name' => 'Matching Game',
            'flag' => 'matching-game',
            'textbook_flag' => 'numbers'
        ]);

        // Quiz Scores
        GameScore::updateOrCreate([
            'game_id' => $hangmanGame->id,
            'perfect_score' => 18,
            'passing_score' => 10,
            'type' => 'quiz'
        ]);

        GameScore::updateOrCreate([
            'game_id' => $typingBalloon->id,
            'perfect_score' => 51,
            'passing_score' => 38,
            'type' => 'quiz'
        ]);

        GameScore::updateOrCreate([
            'game_id' => $memoryGame->id,
            'perfect_score' => 59,
            'passing_score' => 44,
            'type' => 'quiz'
        ]);

        GameScore::updateOrCreate([
            'game_id' => $matchingGame->id,
            'perfect_score' => 24,
            'passing_score' => 18,
            'type' => 'quiz'
        ]);

        // Tutorial Scores
        GameScore::updateOrCreate([
            'game_id' => $hangmanGame->id,
            'perfect_score' => 8,
            'passing_score' => 6,
            'type' => 'tutorial'
        ]);

        GameScore::updateOrCreate([
            'game_id' => $typingBalloon->id,
            'perfect_score' => 26,
            'passing_score' => 20,
            'type' => 'tutorial'
        ]);

        GameScore::updateOrCreate([
            'game_id' => $memoryGame->id,
            'perfect_score' => 12,
            'passing_score' => 9,
            'type' => 'tutorial'
        ]);

        GameScore::updateOrCreate([
            'game_id' => $matchingGame->id,
            'perfect_score' => 12,
            'passing_score' => 9,
            'type' => 'tutorial'
        ]);
    }
}
