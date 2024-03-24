<?php

namespace App\Http\Controllers;

use App\Http\Requests\AlphabetRequest;
use App\Http\Requests\TextbookRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Textbook;
use App\Rules\UniqueDependingOnFlag;
use Closure;
use DateTime;
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
            ->orderBy('letter', 'ASC')
            ->get()
            ->map(function ($textbooks) {
                $textbooks->image_url = json_decode($textbooks->image);
                $textbooks->video_url = json_decode($textbooks->video);
                return $textbooks;
            })->toArray();

        $vowels = array_filter($textbooks, function ($textbook) {
            return $textbook["type"] == 'vowel';
        });
        $consonants = array_filter($textbooks, function ($textbook) {
            return $textbook["type"] == 'consonant';
        });
        if (count($vowels) || count($consonants)) {
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
            ->orderBy('letter', 'ASC')
            ->get()
            ->map(function ($textbooks) {
                $textbooks->image_url = json_decode($textbooks->image);
                $textbooks->video_url = json_decode($textbooks->video);
                return $textbooks;
            })->toArray();
    }

    public function getAlphabetsWords(Request $request)
    {
        if ($request->get('user') == 'student') {
            $teacherId = Student::where('user_id', Auth::user()->id)->first()->teacher_id;
        } else {
            $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        }
        return Textbook::where('teacher_id', $teacherId)
            ->where('flag', 'alphabet-words')
            ->where('chapter', $request->get('chapter'))
            ->orderBy('letter', 'ASC')
            ->get()
            ->map(function ($textbooks) {
                $textbooks->image_url = json_decode($textbooks->image);
                $textbooks->video_url = json_decode($textbooks->video);
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
            $storagePath = null;
            if ($request->flag == 'alphabet-letters') {
                $storagePath = 'alphabets-letters';
            } else if ($request->flag == 'vowel-consonants') {
                $storagePath = 'vowels-consonants';
            } else {
                $storagePath = 'alphabets-words';
            }

            $expiresAt = new DateTime();
            $expiresAt->modify('+1 year');

            $image = $request->file('image'); //image file from frontend
            $video = $request->file('video'); //video file from frontend

            $firebase_image_path = $storagePath . "/images/";
            $firebase_video_path = $storagePath . "/videos/";

            $imageName = strtoupper($request->letter) . rand(100000, 999999);
            $videoName = strtoupper($request->letter) . rand(100000, 999999);

            $localfolder = public_path('firebase-temp-uploads') . '/';

            $imageExtension = $image->getClientOriginalExtension();
            $videoExtension = $video->getClientOriginalExtension();

            $imageFile      = $imageName . '.' . $imageExtension;
            $videoFile      = $videoName . '.' . $videoExtension;

            $image->move($localfolder, $imageFile);
            $video->move($localfolder, $videoFile);

            $imageUploadedfile = fopen($localfolder . $imageFile, 'r');
            $videoUploadedfile = fopen($localfolder . $videoFile, 'r');

            $imagePath = app('firebase.storage')->getBucket()->upload($imageUploadedfile, [
                'name' => $firebase_image_path . $imageFile
            ])->signedUrl($expiresAt);
            $videoPath = app('firebase.storage')->getBucket()->upload($videoUploadedfile, [
                'name' => $firebase_video_path . $videoFile
            ])->signedUrl($expiresAt);

            unlink($localfolder . $imageFile);
            unlink($localfolder . $videoFile);

            // $imageFile = $request->file('image');
            // $imageFileName = $imageFile->getClientOriginalName();
            // $imagePath = $imageFile->storeAs($storagePath . '/images', $imageFileName, 'public');

            // $videoFile = $request->file('video');
            // $videoFileName = $videoFile->getClientOriginalName();
            // $videoPath = $videoFile->storeAs($storagePath . '/videos', $videoFileName, 'public');

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

    public function deleteTextbook($textbookId)
    {
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
        $letter = request()->letter;
        $firstLetter = request()->objectName[0];
        return $firstLetter === $letter;
    }

    public function seedTextbook()
    {
        $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        Textbook::where('teacher_id', $teacherId)->delete();
        $jsonFile = public_path('json/alphabets-with-letters.json');
        $jsonData = json_decode(file_get_contents($jsonFile), true);

        foreach ($jsonData as $data) {
            if (in_array($data["letter"], ['a', 'e', 'i', 'o', 'u']))
                $type = 'vowel';
            else
                $type = 'consonant';

            Textbook::updateOrCreate([
                'flag' => 'alphabet-letters',
                'type' => $type,
                'teacher_id' => $teacherId,
                'letter' => $data["letter"],
                'object' => $data["object"],
                'image' => json_encode($data["image"]),
                'video' => json_encode($data["video"]),
                'chapter' => 1
            ]);
        }

        $jsonFile = public_path('json/vowel-consonants.json');
        $jsonData = json_decode(file_get_contents($jsonFile), true);

        foreach ($jsonData as $json) {
            foreach ($json as $data) {
                if (in_array($data["letter"], ['a', 'e', 'i', 'o', 'u']))
                    $type = 'vowel';
                else
                    $type = 'consonant';

                Textbook::updateOrCreate([
                    'flag' => 'vowel-consonants',
                    'type' => $type,
                    'teacher_id' => $teacherId,
                    'letter' => $data["letter"],
                    'object' => $data["object"],
                    'image' => json_encode($data["image"]),
                    'video' => json_encode($data["video"]),
                    'chapter' => 1
                ]);
            }
        }

        $jsonFile = public_path('json/alphabets-with-words.json');
        $jsonData = json_decode(file_get_contents($jsonFile), true);

        foreach ($jsonData as $data) {
            if (in_array($data["letter"], ['a', 'e', 'i', 'o', 'u']))
                $type = 'vowel';
            else
                $type = 'consonant';

            Textbook::updateOrCreate([
                'flag' => 'alphabet-words',
                'type' => $type,
                'teacher_id' => $teacherId,
                'letter' => $data["letter"],
                'object' => $data["object"],
                'image' => json_encode($data["image"]),
                'video' => json_encode($data["video"]),
                'chapter' => 1
            ]);
        }

        return response()->json([
            'message' => 'Textbooks are successfully inserted.'
        ]);
    }
}
