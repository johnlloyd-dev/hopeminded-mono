<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlphabetRequest;
use App\Http\Requests\TextbookRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Textbook;
use App\Rules\UniqueDependingOnFlag;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Laravel\Ui\Presets\React;
use stdClass;

class TextbookController extends Controller
{
    public function getVowelsConsonants(Request $request)
    {
        if ($request->get('user') == 'student') {
            $teacherId = Student::where('user_id', Auth::user()->id)->first()->teacher_id;
        } else {
            $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        }
        $textbooks = Textbook::where('teacher_id', $teacherId)
            ->where('flag', 'vowel-consonants')
            ->where('chapter', $request->get('chapter'))
            ->get()
            ->map(function ($textbooks) {
                $textbooks->image_url = Storage::url(json_decode($textbooks->image));
                $textbooks->video_url = Storage::url(json_decode($textbooks->video));
                return $textbooks;
            })->toArray();

        $vowels = array_filter($textbooks, function ($textbook) {
            return $textbook["type"] == 'vowel';
        });
        $consonants = array_filter($textbooks, function ($textbook) {
            return $textbook["type"] == 'consonant';
        });
        if(count($vowels) || count($vowels)) {
            return [array_values($vowels), array_values($consonants)];
        } else {
            return [];
        }


    }

    public function getAlphabetsLetters(Request $request)
    {
        if ($request->get('user') == 'student') {
            $teacherId = Student::where('user_id', Auth::user()->id)->first()->teacher_id;
        } else {
            $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        }
        return Textbook::where('teacher_id', $teacherId)
            ->where('flag', 'alphabet-letters')
            ->where('chapter', $request->get('chapter'))
            ->get()
            ->map(function ($textbooks) {
                $textbooks->image_url = Storage::url(json_decode($textbooks->image));
                $textbooks->video_url = Storage::url(json_decode($textbooks->video));
                return $textbooks;
            })->toArray();
    }

    public function getAlphabetsWords(Request $request) {
        if ($request->get('user') == 'student') {
            $teacherId = Student::where('user_id', Auth::user()->id)->first()->teacher_id;
        } else {
            $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        }
        return Textbook::where('teacher_id', $teacherId)
            ->where('flag', 'alphabet-words')
            ->where('chapter', $request->get('chapter'))
            ->get()
            ->map(function ($textbooks) {
                $images = json_decode($textbooks->image);
                $videos = json_decode($textbooks->video);
                $objectNames = json_decode($textbooks->object);
                foreach ($objectNames as $key => $value) {
                    $data[] = [
                        "object" => $value,
                        "image" => Storage::url($images[$key]),
                        "video" => Storage::url($videos[$key]),
                    ];
                }
                $textbooks['attributes'] = $data;
                return $textbooks;
            })->toArray();
    }

    public function addTextbook(TextbookRequest $request)
    {
        if (self::isLetterExist()) {
            return response()->json([
                'errors' => [
                    'letter' => ["This letter already exist."]
                ]
            ], 422);
        } else if (!self::isFirstLetterMatch($request->flag)) {
            return response()->json([
                'errors' => [
                    'objectName' => ["The first letter of the object name and the entered letter did not match."]
                ]
            ], 422);
        }

        if ($request->has('image') && $request->has('video')) {
            if ($request->flag == 'alphabet-letters') {
                $storagePath = 'alphabets-letters';
            } else if ($request->flag == 'vowel-consonants') {
                $storagePath = 'vowels-consonants';
            }
            $imageFile = $request->file('image');
            $imageFileName = $imageFile->getClientOriginalName();
            $imagePath = $imageFile->storeAs(`{$storagePath}/images`, $imageFileName, 'public');

            $videoFile = $request->file('video');
            $videoFileName = $videoFile->getClientOriginalName();
            $videoPath = $videoFile->storeAs(`{$storagePath}/videos`, $videoFileName, 'public');

            $alphabetType = in_array($request->letter, ['a', 'e', 'i', 'o', 'u']) ? 'vowel' : 'consonant';

            $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
            Textbook::create([
                'flag' => $request->flag,
                'letter' => strtolower($request->letter),
                'object' => $request->objectName,
                'image' => json_encode($imagePath),
                'video' => json_encode($videoPath),
                'type' => $alphabetType,
                'teacher_id' => $teacherId,
                'chapter' => $request->get('chapter')
            ]);

            return response()->json(['message' => 'An alphabet is added successfully.']);
        }
    }

    public function addTextbookAlphabetWords(Request $request)
    {
        // return json_decode($request->objectName);
        $objectNames = array_filter(json_decode($request->objectName), function ($value) {
            return $value == null;
        });

        $objectNames2 = array_filter(json_decode($request->objectName), function ($value) {
            return $value == "";
        });

        if ($request->letter == null) {
            return response()->json([
                'errors' => [
                    'letter' => ["Alphabet field is required."]
                ]
            ], 422);
        }

        if (!empty($objectNames) || !empty($objectNames2)) {
            return response()->json([
                'errors' => [
                    'letter' => ["Object name fields are required."]
                ]
            ], 422);
        }

        if (!$request->has('image') || !$request->has('video')) {
            return response()->json([
                'errors' => [
                    'letter' => ["Object image or video fields are required."]
                ]
            ], 422);
        }

        if (self::isLetterExist()) {
            return response()->json([
                'errors' => [
                    'letter' => ["This letter already exist."]
                ]
            ], 422);
        }

        foreach (json_decode($request->objectName) as $key => $value) {
            $imageFile[] = $request->file('image')[$key];
            $imageFileName[] = $imageFile[$key]->getClientOriginalName();
            $imagePath[] = $imageFile[$key]->storeAs('alphabets-words/' . 'images/' . strtoupper($request->letter), $imageFileName[$key], 'public');

            $videoFile[] = $request->file('video')[$key];
            $videoFileName[] = $videoFile[$key]->getClientOriginalName();
            $videoPath[] = $videoFile[$key]->storeAs('alphabets-words/' . 'videos/' . strtoupper($request->letter), $videoFileName[$key], 'public');
        }

        $alphabetType = in_array($request->letter, ['a', 'e', 'i', 'o', 'u']) ? 'vowel' : 'consonant';

        $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        Textbook::create([
            'flag' => $request->flag,
            'letter' => strtolower($request->letter),
            'object' => $request->objectName,
            'image' => json_encode($imagePath),
            'video' => json_encode($videoPath),
            'type' => $alphabetType,
            'teacher_id' => $teacherId,
            'chapter' => $request->get('chapter')
        ]);

        return response()->json(['message' => 'An alphabet is added successfully.']);
    }

    public function deleteTextbook($textbookId) {
        Textbook::find($textbookId)->delete();
        return response()->json(['message' => 'An alphabet is deleted successfully.']);
    }

    public function isLetterExist()
    {
        $teacher = Teacher::where('user_id', Auth::user()->id)->first();
        $count = Textbook::where('flag', request()->flag)
            ->where('teacher_id', $teacher->id)
            ->where('chapter', request()->get('chapter'))
            ->where('letter', request()->letter)
            ->count();

        return $count > 0;
    }

    public function isFirstLetterMatch($flag)
    {
        if ($flag != 'alphabet-words') {
            $letter = request()->letter;
            $firstLetter = request()->objectName[0];
            return $firstLetter === $letter;
        }
    }
}
