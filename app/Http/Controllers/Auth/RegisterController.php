<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\FlagAttribute;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $teacher = Teacher::find($request->teacherId);
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'user_flag' => $request->userFlag
        ]);
        $teacher->update([
            'user_id' => $user->id,
            // 'gender' => $request->gender,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'is_registered' => 1
        ]);
        return response()->json(['message' => 'You have successfully registered.']);
    }
}
