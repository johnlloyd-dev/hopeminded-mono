<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'email',
        'is_registered',
        'teacher_id',
        'user_id',
        'unhashed',
        'section_id'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
