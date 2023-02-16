<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Person;
use Faker\Generator as Faker;

$factory->define(Person::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'document' =>  $faker->ein,
        'ima_profile' => $faker->imageUrl(800, 400, 'food', true, 'Faker'),
        'type_person' => 0
    ];
});
