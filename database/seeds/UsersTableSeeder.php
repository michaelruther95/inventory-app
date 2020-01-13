<?php

use Illuminate\Database\Seeder;
use App\User;
use App\UserRole;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->profile = [
        	'first_name' => 'John',
        	'last_name' => 'Doe',
        	'gender' => 'Male',
        ];
        $user->email = 'inventoryapp.smtp@gmail.com';
        $user->password = bcrypt('123123');
        $user->save();

        $userRole = new UserRole();
        $userRole->role_id = 1;
        $userRole->user_id = 1;
        $userRole->save();

        $user = new User();
        $user->profile = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'gender' => 'Female',
        ];
        $user->email = 'valeriebantilan@gmail.com';
        $user->password = bcrypt('123123');
        $user->save();

        $userRole = new UserRole();
        $userRole->role_id = 2;
        $userRole->user_id = 2;
        $userRole->save();
    }
}
