<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Favorite;
use Faker\Generator as Faker;

$factory->define(Favorite::class, function (Faker $faker) {
    $action = App\Action::doesntHave('favorite')->inRandomOrder()->first();
    if(!$action) {
        $action = factory(App\Action::class)->create();
    }
    
    $sensor = App\Sensor::doesntHave('favorite')->inRandomOrder()->first();
    if(!$sensor) {
        $sensor = factory(App\Sensor::class)->create();
    }

    $type = rand(0,1) === 1 ? App\Sensor::class : App\Action::class;

    return [
        'favoriteable_id' => $type == App\Sensor::class ? $sensor->id : $action->id,
        'favoriteable_type' => $type,
        'order' => rand(1,100),
        'name' => $faker->word()
    ];
});
