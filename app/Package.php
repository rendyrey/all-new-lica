<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Package extends Model
{
    protected $with = ['package_tests'];
    protected $fillable = [
        'name',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'name' => 'required',
            'general_code' => 'required',
        ]);
    }

    public function package_tests()
    {
        return $this->hasMany('App\PackageTest', 'package_id', 'id');
    }
}
