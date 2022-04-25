<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionTest extends Model
{
    protected $fillable = [
        'transaction_id',
        'test_id',
        'package_id',
        'price_id',
        'group_id',
        'type'
    ];
}
