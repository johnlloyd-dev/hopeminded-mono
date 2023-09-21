<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        if ($request->query('user_flag') === 'teacher') {
            $teacher_id = Teacher::where('user_id', Auth::user()->id)->first()->id;
            $student_ids = Student::where('teacher_id', $teacher_id)->pluck('id');
            return Notification::whereIn('student_id', $student_ids)
                ->where('subject', 'for_teacher')
                ->orderBy('status', 'desc')
                ->get()
                ->map(function ($data) {
                    $notification_content = null;
                    $student = Student::find($data->student_id);
                    $student_name = $student->last_name . ', ' . $student->first_name . ' ' . ($student->middle_name ? substr($student->middle_name, 0, 1) . '.' : '');
                    $attributes = json_decode($data->attributes);
                    switch ($data->flag) {
                        case 'fs_first_take_quiz':
                            $game_name = Game::find($attributes->game_id)->name;
                            $notification_content = $student_name . ' made his first attempt for the ' . $game_name . ' quiz of ' . $attributes->textbook_name . ' textbook.';
                            break;

                        case 'fs_retake_quiz':
                            $game_name = Game::find($attributes->game_id)->name;
                            $notification_content = $student_name . ' has retaken the ' . $game_name . ' quiz of ' . $attributes->textbook_name . ' textbook.';
                            break;

                        case 'fs_first_skill_test':
                            $notification_content = $student_name . ' has submitted a skill test for the alphabet ' . strtoupper($attributes->alphabet) . ' of ' . $attributes->textbook_name . ' textbook.';
                            break;

                        case 'fs_resubmit_skill_test':
                            $notification_content = $student_name . ' has submitted a skill test for the alphabet ' . strtoupper($attributes->alphabet) . ' of ' . $attributes->textbook_name . ' textbook.';
                            break;
                    }
                    $data->notification_content = $notification_content;

                    return $data;
                });
        } else {
            return Notification::where('student_id', Student::where('user_id', Auth::user()->id)->first()->id)
                ->where('subject', 'for_student')
                ->get()
                ->map(function ($data) {
                    $notification_content = null;
                    $attributes = json_decode($data->attributes);
                    switch ($data->flag) {
                        case 'ft_retake_quiz':
                            $game_name = Game::find($attributes->game_id)->name;
                            $notification_content = 'You are given another chance to retake the quiz for the ' . $game_name . ' of ' . $attributes->textbook_name . ' textbook.';
                            break;

                        case 'ft_retake_skill_test':
                            $notification_content = 'You are given another chance to resubmit a skill test for each alphabet.';
                            break;
                    }
                    $data->notification_content = $notification_content;

                    return $data;
                });
        }
    }

    public function updateNotification($notificationId)
    {
        $notification = Notification::find($notificationId);
        $notification->status = 'read';
        $notification->save();

        return 'Notification successfully updated.';
    }
}
