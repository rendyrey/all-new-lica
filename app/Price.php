<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = [
        'package_id',
        'type',
        'price',
        'price_class'
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
