<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $with = ['patient','room','insurance', 'doctor'];
    protected $fillable = [
        'patient_id',
        'room_id',
        'doctor_id',
        'insurance_id',
        'analyzer_id',
        'type',
        'no_lab',
        'memo',
        'created_time',
        'cito',
        'transaction_id_label'
    ];

    public function patient()
    {
        return $this->belongsTo('App\Patient', 'patient_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo('App\Room', 'room_id', 'id');
    }

    public function insurance()
    {
        return $this->belongsTo('App\Insurance', 'insurance_id', 'id');
    }

    public function doctor()
    {
        return $this->belongsTo('App\Doctor','doctor_id','id');
    }
}
