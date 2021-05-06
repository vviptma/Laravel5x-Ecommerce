<?php

use Illuminate\Database\Seeder;
use App\Roles;
class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Roles::truncate();

        Roles::create(['name'=>'admin']);
        Roles::create(['name'=>'author']);
        Roles::create(['name'=>'user']);
      
    }
}
