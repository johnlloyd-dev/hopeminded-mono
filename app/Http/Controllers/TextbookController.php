<?php

namespace App\Http\Controllers;

use App\Http\Requests\TextbookRequest;
use App\Models\Teacher;
use App\Models\Textbook;
use App\Rules\UniqueDependingOnFlag;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TextbookController extends Controller
{
    public function addTextbook(TextbookRequest $request)
    {
        if (self::isLetterExist()) {
            return response()->json([
                'errors' => [
                    'letter' => ["This letter already exist."]
                ]
            ], 422);
        } else if (!self::isFirstLetterMatch()) {
            return response()->json([
                'errors' => [
                    'objectName' => ["The first letter of the object name and the entered letter did not match."]
                ]
            ], 422);
        }
        
        if ($request->has('image') && $request->has('video')) {
            $imageFile = $request->file('image');
            $imageFileName = $imageFile->getClientOriginalName();
            $imagePath = $imageFile->storeAs('alphabets-letters/images', $imageFileName, 'public');

            $videoFile = $request->file('video');
            $videoFileName = $videoFile->getClientOriginalName();
            $videoPath = $videoFile->storeAs('alphabets-letters/videos', $videoFileName, 'public');

            $alphabetType = in_array($request->letter, ['a', 'e', 'i', 'o', 'u']) ? 'vowel' : 'consonant';

            $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
            Textbook::create([
                'flag' => $request->flag,
                'letter' => strtolower($request->letter),
                'object' => $request->objectName,
                'image' => json_encode($imagePath),
                'video' => json_encode($videoPath),
                'type' => $alphabetType,
                'teacher_id' => $teacherId
            ]);

            return response()->json(['message' => 'An alphabet is added successfully.']);
        }
    }

    public function isLetterExist()
    {
        $count = Textbook::where('flag', request()->flag)
            ->where('letter', request()->letter)
            ->count();

        return $count > 0;
    }

    public function isFirstLetterMatch() {
        $letter = request()->letter;
        $firstLetter = request()->objectName[0];

        return $firstLetter === $letter;
    }
}
