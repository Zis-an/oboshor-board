<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'user_id' => 'user1234',
            'password' => bcrypt('123456'),
        ]);

        //add role

        $role = Role::create([
            'name' => 'Super Admin'
        ]);

        $user->assignRole($role);

    }
}
