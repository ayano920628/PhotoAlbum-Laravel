<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Image;
use Faker\Generator as Faker;

$factory->define(Image::class, function (Faker $faker) {
    return [
      'user_id' => '1',
      'img_name' => 'sample.jpg',
      'img_comment' => 'aiueo',
      'taken' => $faker->dateTime(),
      'created_at' => $faker->dateTime(),
      'updated_at' => $faker->dateTime(),
    ];
});
