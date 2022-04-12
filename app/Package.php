<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Package extends Model
{
    protected $fillable = [
        'name',
        'price',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'name' => 'required',
            'price' => 'required',
            'general_code' => 'required',
        ]);
    }
}
