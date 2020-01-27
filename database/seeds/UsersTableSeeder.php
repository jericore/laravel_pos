<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
          'name'      => 'Jerico Reynaldi',
          'email'     => 'jerico.reynaldi@gmail.com',
          'password'  => bcrypt('secret'),
          'status'    => true
        ]);
    }
}
