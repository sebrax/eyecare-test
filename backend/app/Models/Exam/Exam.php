<?php

namespace App\Models\Exam;

use App\Enums\Exam\GroupEnum;
use App\Enums\Exam\LateralityEnum;
use App\Models\Package\Package;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $fillable = ['name', 'laterality', 'comment', 'group'];

    protected $casts = [
        'laterality' => LateralityEnum::class,
        'group' => GroupEnum::class,
    ];

    public function packages()
    {
        return $this->belongsToMany(Package::class);
    }
}
