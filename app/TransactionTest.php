<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionTest extends Model
{
    protected $with = ['test'];
    protected $fillable = [
        'transaction_id',
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
}
