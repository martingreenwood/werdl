<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{

    protected $table = 'words';
    protected $fillable = ['word', 'active_on'];
    protected $dates = ['active_on'];
    protected $casts = ['active_on' => 'date'];

}
