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

    protected $with = ['package'];

    public function package()
    {
        return $this->belongsTo('App\Package', 'package_id', 'id');
    }

    protected $fillable = [
        'package_id',
        'type',
        'price',
        'class'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'package_id' => 'required',
            'type' => 'required',
            'price' => 'required',
            'class' => 'required'
        ]);
    }

}
