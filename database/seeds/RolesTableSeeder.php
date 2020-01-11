<?php

use Illuminate\Database\Seeder;
use App\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
        	(object)['name' => 'super-admin', 'display_name' => 'super admin'],
        	(object)['name' => 'employee', 'display_name' => 'employee']
        ];

        foreach($roles as $roleItem){
        	$role = new Role();
	        $role->name = $roleItem->name;
	        $role->display_name = $roleItem->display_name;
	        $role->save();
        }
    }
}
