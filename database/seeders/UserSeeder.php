<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $organization = Organization::create([
            'name'=>'kara.ai',
            'currency'=> 'EUR'
        ]);

        $user = User::create([
           'id' => 1,
           'role_id' => 1,
           'name' => 'admin',
           'email' => 'admin@kara.ai',
           'password' => bcrypt('123456789'),
           'active' => true
        ]);

        $user->organizations()->attach($organization);

    }
}
