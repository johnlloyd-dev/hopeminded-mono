<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\Textbook;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TextbookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonFile = Storage::path('public/json/alphabets-with-letters.json');
        $jsonData = json_decode(file_get_contents($jsonFile), true);
        
        foreach ($jsonData as $data) {
            if(in_array($data->letter, ['a', 'b', 'c', 'd', 'e']))
                $type = 'vowel';
            else
                $type = 'consonant';
            $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
            Textbook::updateOrCreate([
                'flag' => 'alphabet-letters', 
                'type' => $type, 
                'teacher_id' => $teacherId, 
                'letter' => $data->letter, 
                'object' => $data->object, 
                'image' => json_encode($data->image), 
                'video' => json_encode($data->video)
            ]);
        }
    }
}
