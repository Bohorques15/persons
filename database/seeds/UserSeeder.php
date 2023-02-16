<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'Pruebas';
        $user->email = 'pruebas@amobasoftware.com';
        $user->password = Hash::make("134679");

        $user->save();
    }
}
