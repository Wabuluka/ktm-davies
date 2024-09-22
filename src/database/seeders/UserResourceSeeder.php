<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->admin(true)->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
        ]);

        User::factory()->admin(false)->create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'email_verified_at' => now(),
        ]);

        $apiUser = User::factory()->admin(false)->create([
            'name' => 'API User',
            'email' => 'api@example.com',
            'email_verified_at' => now(),
        ]);

        $apiUser->createToken('graphql');
        $apiUser->tokens()->first()->update([
            // 1|cZgS0RKxYJEjDFUJCJg64aIwm86oVcaPBkLYnD0y
            'token' => '70c2ea16ce0cd43d78a3be0f21a9fc8ec85aa6375dbe89be285d301149b291f6',
        ]);
    }
}
