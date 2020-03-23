<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;

$factory->define(\App\Models\CastMember::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'type' => array_rand([\App\Models\CastMember::TYPE_ACTOR, \App\Models\CastMember::TYPE_DIRECTOR]),
    ];
});
