<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Doctor;
use Faker\Generator as Faker;

$factory->define(Doctor::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'title' => $faker->lexify('Dr?'),
        'general_code' => $faker->randomNumber(3, true)
    ];
});
