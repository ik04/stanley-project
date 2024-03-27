<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageProcessing extends Model
{
    use HasFactory;
    protected $fillable = [
        "technique",
        "value",
        "image_id",
    ];
}
