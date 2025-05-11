<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'android_version',
        'android_build',
        'ios_version',
        'ios_build',
        'ios_message',
        'android_message'
    ];
}
