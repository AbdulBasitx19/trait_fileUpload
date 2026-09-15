<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $fillable =[
        'title',
        'original_name',
        'file_path',
        'disk',
        'mime_type',
        'file_size',
    ];
}
