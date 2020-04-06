<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\CastMember::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'type' => array_rand(array_flip([\App\Models\CastMember::TYPE_DIRECTOR, \App\Models\CastMember::TYPE_ACTOR])),
    ];
});
