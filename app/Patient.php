<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'medrec',
        'name',
        'gender',
        'birthdate',
        'address',
        'phone',
        'email'
    ];
}
