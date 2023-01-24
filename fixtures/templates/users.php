<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */
return [
    'name' => $faker->name(),
    'lastName' => $faker->lastName(),
    'email' => $faker->email(),
    'password' => Yii::$app->getSecurity()->generatePasswordHash('123456'),
    'avatarSrc' => $faker->randomElement([
        '/img/avatar01.jpg',
        '/img/avatar02.jpg',
        '/img/avatar03.jpg',
        '/img/avatar04.jpg',
    ])
];
