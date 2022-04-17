<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Room extends Model
{
    // rawat inap, rawat jalan, IGD
    const TYPE = [
        'rawat_inap' => 'Rawat Inap',
        'rawat_jalan' => 'Rawat Jalan',
        'igd' => 'IGD',
        'rujukan' => 'Rujukan'
    ];
    
    // const TYPE = [
    //     '1' => 'Satu',
    //     '2' => 'Dua',
    //     '3' => 'Tiga',
    //     '4' => 'Empat'
    // ];

    protected $fillable = [
        'room',
        'room_code',
        'class',
        'auto_checkin',
        'auto_draw',
        'type',
        'referral_address',
        'referral_no_phone',
        'referral_email',
        'general_code'
    ];

    public static function validate($request)
    {
        return Validator::make($request->all(),
        [
            'room' => 'required',
            'room_code' => 'required',
            'class' => 'required',
            'type' => 'required',
            'referral_address' => 'required',
            'referral_no_phone' => 'required',
            'referral_email' => 'required',
            'general_code'
        ]);
        
    }

}
