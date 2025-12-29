<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Crud extends Model
{
    // 🔹 Table name (VERY IMPORTANT)
    protected $table = 'crud';

    // 🔹 Mass assignment allow
    protected $fillable = [
        'title',
        'description'
    ];
}
