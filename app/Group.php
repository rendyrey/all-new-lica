<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Group extends Model
{
    protected $fillable = [
        'name',
        'early_limit',
        'limit',
        'general_code'
    ];

    public function analyzers() {
        // the format for one to many (inverse)
        // return $this->belongsTo('App\User', 'foreign_key', 'other_key');

        // the format for one to many
        // return $this->hasMany('App\Comment', 'foreign_key', 'local_key');
        return $this->hasMany('App\Analyzer','group_id','id');
    }

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'name' => 'required',
            'early_limit' => 'required|numeric',
            'limit' => 'required|numeric',
            'general_code' => 'required'
        ]);
    }
}
