<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\FlagAttribute;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $attributes = [
            "a" => false,
            "b" => false,
            "c" => false,
            "d" => false,
            "e" => false,
            "f" => false,
            "g" => false,
            "h" => false,
            "i" => false,
            "j" => false,
            "k" => false,
            "l" => false,
            "m" => false,
            "n" => false,
            "o" => false,
            "p" => false,
            "q" => false,
            "r" => false,
            "s" => false,
            "t" => false,
            "u" => false,
            "v" => false,
            "w" => false,
            "x" => false,
            "y" => false,
            "z" => false
        ];
        $attributes2 = [
            "a" => [],
            "b" => [],
            "c" => [],
            "d" => [],
            "e" => [],
            "f" => [],
            "g" => [],
            "h" => [],
            "i" => [],
            "j" => [],
            "k" => [],
            "l" => [],
            "m" => [],
            "n" => [],
            "o" => [],
            "p" => [],
            "q" => [],
            "r" => [],
            "s" => [],
            "t" => [],
            "u" => [],
            "v" => [],
            "w" => [],
            "x" => [],
            "y" => [],
            "z" => []
        ];
        // if ($request->userFlag == 'teacher') {
        //     User::create([
        //         'first_name' => $request->firstName,
        //         'middle_name' => $request->middleName,
        //         'last_name' => $request->lastName,
        //         'email' => $request->email,
        //         'username' => $request->username,
        //         'password' => Hash::make($request->password),
        //         'is_registered' => 1,
        //         'user_flag' => $request->userFlag
        //     ]);
        // } else {
            $student = Student::find($request->studentId);
            $user = User::create([
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'user_flag' =>$request->userFlag
            ]);
            $student->update([
                'user_id' => $user->id,
                'gender' => $request->gender,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'is_registered' => 1
            ]);

            FlagAttribute::insert([
                'student_id' => $student->id,
                'flag' => 'alphabet-letters',
                'attributes' => json_encode($attributes),
            ]);

            FlagAttribute::insert([
                'student_id' => $student->id,
                'flag' => 'alphabet-words',
                'attributes' => json_encode($attributes2),
            ]);
            FlagAttribute::insert([
                'student_id' => $student->id,
                'flag' => 'vowel-consonants',
                'attributes' => json_encode($attributes),
            ]);
        // }
        return response()->json(['message' => 'You have successfully registered.']);
    }
}
