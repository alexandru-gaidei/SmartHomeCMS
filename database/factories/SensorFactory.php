<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Sensor;
use Faker\Generator as Faker;

$factory->define(Sensor::class, function (Faker $faker) {
    $src_types = array_keys(Sensor::$SRC_TYPES);
    shuffle($src_types);
    $src_type = array_pop($src_types);

    $val_types = array_keys(Sensor::$VAL_TYPES);
    shuffle($val_types);
    $val_type = array_pop($val_types);

    $groups = App\Group::all();
    if($groups->count() == 0) {
        $groups = factory(App\Group::class, 2)->create();
    }

    return [
        'group_id' => $groups->random()->id,
        'name' => $faker->word(),
        'source_type' => $src_type,
        'source_url_fetch' => $src_type == Sensor::SRC_TYPE_FETCH ? $faker->url : null,
        'parameter' => $faker->word(),
        'identifier' => $src_type == Sensor::SRC_TYPE_PUSH ? $faker->uuid : null,
        'value_type' => $val_type, 
        'execute_at_rrule' => 'FREQ=DAILY;INTERVAL=2',
        'min_value' => rand(1,5),
        'value' => rand(1,20),
        'max_value' => rand(15,20),
    ];
});
