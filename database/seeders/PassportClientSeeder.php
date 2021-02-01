<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PassportClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createPersonalClient();
        $this->createPasswordClient();
    }

    private function createPersonalClient()
    {
        DB::table('oauth_clients')->insert([
            'user_id' => null,
            'name' => 'Laravel Personal Access Client',
            'secret' => 'QMjclllvWwcNcpasoCKc20iLARaoI7KV6bWA2Z2i',
            'provider' => null,
            'redirect' => env('APP_URL'),
            'personal_access_client' => 1,
            'password_client' => 0,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('oauth_personal_access_clients')->insert([
            'client_id' => '1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function createPasswordClient()
    {
        DB::table('oauth_clients')->insert([
            'user_id' => null,
            'name' => 'Laravel Password Grant Client',
            'secret' => 'c1JwDig0UF7npaa3Z6xhcUOC6n72bEaCNKpbtofO',
            'provider' => 'users',
            'redirect' => env('APP_URL'),
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
