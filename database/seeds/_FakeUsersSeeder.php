<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class _FakeUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\User::create([
            'name' => 'Admin',
            'email' => 'admin@sh-cms.com',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ]);

        factory(App\User::class, 24)->create();
    }
}
