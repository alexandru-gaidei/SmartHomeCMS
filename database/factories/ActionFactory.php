<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Action;
use Faker\Generator as Faker;

$factory->define(Action::class, function (Faker $faker) {
    $val_types = array_keys(Action::$VAL_TYPES);
    shuffle($val_types);
    $val_type = array_pop($val_types);

    $types = array_keys(Action::$TYPES);
    shuffle($types);
    $type = array_pop($types);

    if($type == Action::TYPE_HTTP_GET) {
        $subject = $faker->url;
    }
    elseif($type == Action::TYPE_MAIL) {
        $subject = $faker->email;
    }
    else {
        $subject = null;
    }

    $sensors = App\Sensor::all();
    if($sensors->count() == 0) {
        $sensors = factory(App\Sensor::class, 2)->create();
    }

    return [
        'sensor_id' => $sensors->random()->id,
        'name' => $faker->word(),
        'value_type' => $val_type,
        'type' => $type,
        'subject' => $subject
    ];
});
