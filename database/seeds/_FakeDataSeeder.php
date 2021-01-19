<?php

use Illuminate\Database\Seeder;

class _FakeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Group::class, 25)->create();
        factory(App\Sensor::class, 25)->create();
        factory(App\Action::class, 25)->create();
        factory(App\History::class, 25)->create();

        foreach(range(1, 25) as $i) {
            factory(App\Favorite::class)->create();
        }
    }
}
