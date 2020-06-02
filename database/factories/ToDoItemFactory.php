<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ToDoItem;
use Faker\Generator as Faker;

$factory->define(ToDoItem::class, function (Faker $faker) {
    return [
        'body' => $faker->text(191),
        'created_at' => now()
    ];
});
