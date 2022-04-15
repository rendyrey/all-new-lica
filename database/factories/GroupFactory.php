<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'early_limit' => $faker->randomNumber(5, false),
        'limit' => $faker->randomNumber(5, false),
        'general_code' => $faker->randomNumber(3, true)
    ];
});
