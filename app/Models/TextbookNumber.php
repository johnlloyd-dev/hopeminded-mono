<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextbookNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'name',
        'value',
        'object',
        'image',
        'video',
        'chapter'
    ];
}
