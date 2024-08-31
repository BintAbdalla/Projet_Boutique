<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientuserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory(3)->client()->create()->each(function ($user) {
            var_dump($user);
            $client = Client::factory()->makeOne();
            $user->client()->save($client);
        });

    }
}
