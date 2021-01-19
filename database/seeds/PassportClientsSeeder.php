<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PassportClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $existing_clients_count = DB::table('oauth_clients')->count();

        if(env('MIX_PASSPORT_CLIENT_ID') && env('MIX_PASSPORT_CLIENT_SECRET') && $existing_clients_count == 0) {
            DB::table('oauth_clients')->insert([
                'id'                     => env('MIX_PASSPORT_CLIENT_ID'),
                'name'                   => config('app.name') . " Password Grant Client",
                'secret'                 => env('MIX_PASSPORT_CLIENT_SECRET'),
                'redirect'               => 'http://localhost',
                'personal_access_client' => false,
                'password_client'        => true,
                'revoked'                => false,
                'created_at'             => now(),
                'updated_at'             => now()
            ]);
        }
    }
}
