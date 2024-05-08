<?php

namespace App\Http\Controllers;

use App\Http\Requests\TextbookNumberRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TextbookNumber;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Rmunate\Utilities\SpellNumber;

class TextbookNumberController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('user') == 'student') {
            $teacherId = Student::where('user_id', Auth::user()->id)->first()->teacher_id;
        } else {
            $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        }

        return TextbookNumber::where('teacher_id', $teacherId)
            ->where('chapter', $request->get('chapter'))
            ->orderBy('value', 'ASC')
            ->get()
            ->map(function ($textbooks) {
                $textbooks->image_url = json_decode($textbooks->image);
                $textbooks->video_url = json_decode($textbooks->video);

                return $textbooks;
            })->toArray();
    }

    public function addTextbookItem(TextbookNumberRequest $request)
    {
        $expiresAt = new DateTime();
        $expiresAt->modify('+1 year');

        $image = $request->file('image'); //image file from frontend
        $video = $request->file('video'); //video file from frontend

        $firebase_image_path = "numbers/images/";
        $firebase_video_path = "numbers/videos/";

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

        $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        $numberWordValue = $this->getWordValue((int)$request->value);

        TextbookNumber::create([
            'teacher_id' => $teacherId,
            'name' => strtolower($numberWordValue),
            'value' => $request->value,
            'object' => $request->object_name,
            'image' => json_encode($imagePath),
            'video' => json_encode($videoPath),
            'chapter' => $request->get('chapter')
        ]);

        return response()->json(['message' => 'A textbook number is added successfully.']);
    }

    public function deleteTextbookItem($textbookId)
    {
        TextbookNumber::find($textbookId)->delete();
        return response()->json(['message' => 'A textbook number is deleted successfully.']);
    }

    public function getWordValue($number)
    {
        $word_value = [
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            0 => 'zero'
        ];

        return $word_value[$number];
    }

    public function seedTextbook()
    {
        $teacherId = Teacher::where('user_id', Auth::user()->id)->first()->id;
        TextbookNumber::where('teacher_id', $teacherId)->delete();

        $jsonFile = public_path('json/textbooks/numbers.json');
        $jsonData = json_decode(file_get_contents($jsonFile), true);

        foreach ($jsonData as $data) {
            TextbookNumber::updateOrCreate([
                'teacher_id' => $teacherId,
                'name' => $data["name"],
                'value' => $data["value"],
                // 'object' => $data["object"],
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
