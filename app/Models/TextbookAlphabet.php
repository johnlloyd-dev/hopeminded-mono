<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextbookAlphabet extends Model
{
    use HasFactory;
    protected $fillable = [
        'flag',
        'type',
        'teacher_id',
        'letter',
        'object',
        'image',
        'video',
        'chapter'
    ];
}
