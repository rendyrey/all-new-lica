<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Price extends Model
{
    
    const TYPE = [
        '1' => 'Satu',
        '2' => 'Dua',
        '3' => 'Tiga',
        '4' => 'Empat'
    ];

    const THE_CLASS = [
        '1' => '1',
        '2' => '2',
        '3' => '3',
    ];

    protected $with = ['test','package'];

    public function package()
    {
        return $this->belongsTo('App\Package', 'package_id', 'id');
    }

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id', 'id');
    }

    protected $fillable = [
        'package_id',
        'test_id',
        'type',
        'price',
        'class'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'type' => 'required',
            'class_price.*.class' => 'required|number',
            'class_price.*.price' => 'required|number'
        ]);
    }

}
