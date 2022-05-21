<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionTest extends Model
{
    protected $with = ['test','transaction'];
    protected $fillable = [
        'draw',
        'draw_time',
        'transaction_id',
        'analyzer_id',
        'test_id',
        'package_id',
        'price_id',
        'group_id',
        'type'
    ];

    public function test()
    {
        return $this->belongsTo('App\Test', 'test_id', 'id');
    }

    public function transaction()
    {
        return $this->belongsTo('App\Transaction', 'transaction_id', 'id');
    }
}
