<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'name' => $faker->realTextBetween(10, 100),
    'typeId' => $faker->numberBetween(1, 2),
    'description' => $faker->realTextBetween(50, 1000),
    'author' => $faker->numberBetween(1, 10),
    'email' => $faker->email(),
    'price' => $faker->numberBetween(100, 100000),
];
