<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Specimen extends Model
{
    protected $table = 'specimens';
    protected $fillable = [
        'name',
        'color',
        'code'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'name' => 'required',
            'color' => 'required',
            'code' => 'required',
        ]);
    }
}
