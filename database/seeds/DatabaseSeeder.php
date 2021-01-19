<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(_FakeUsersSeeder::class);
        $this->call(_FakeDataSeeder::class);
        $this->call(PassportClientsSeeder::class);
    }
}
