<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ToDoItem;
use Faker\Generator as Faker;

$factory->define(ToDoItem::class, function (Faker $faker) {
    return [
        'body' => 'This is an example of an item that I need to do.',
        'created_at' => now()
    ];
});
