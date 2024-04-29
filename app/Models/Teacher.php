<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'user_id',
        'access_id'
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
