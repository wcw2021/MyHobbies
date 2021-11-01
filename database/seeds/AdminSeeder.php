<?php

use Illuminate\Database\Seeder;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = new User([
            'name' => 'Martin',
            'email' => 'weiwu0937@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('test 123'),
            'remember_token' => Str::random(10),
            'role' => 'admin'
        ]);

        $admin->save();
    }
}
