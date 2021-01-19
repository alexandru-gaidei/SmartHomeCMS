<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\History;
use Faker\Generator as Faker;

$factory->define(History::class, function (Faker $faker) {
    $statuses = [History::OK, History::FAIL];
    shuffle($statuses);
    $status = array_pop($statuses);

    $actions = App\Action::all();
    if($actions->count() == 0) {
        $actions = factory(App\Action::class, 30)->create();
    }
    
    $sensors = App\Sensor::all();
    if($sensors->count() == 0) {
        $sensors = factory(App\Sensor::class, 30)->create();
    }

    $type = rand(0,1) === 1 ? App\Sensor::class : App\Action::class;

    return [
        'historyable_id' => $type === App\Sensor::class ? $sensors->random()->id : $actions->random()->id,
        'historyable_type' => $type,
        'status' => $status,
        'data' => $faker->sentence(),
        'value' => $status === History::OK ? rand(1, 100) : null,
        'ocurrence_at' => $faker->date()
    ];
});
