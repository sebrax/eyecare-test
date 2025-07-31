<?php

namespace App\Models\Package;

use App\Models\Exam\Exam;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['name', 'observations'];

    public function exams()
    {
        return $this->belongsToMany(Exam::class);
    }
}
