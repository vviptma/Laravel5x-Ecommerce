<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Admin;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use App\Roles;
/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Admin::class, function (Faker $faker) {
    return [
        'admin_name' => $faker->name,
        'admin_email' => $faker->unique()->safeEmail,
        'admin_phone' => '0932023992',
        'admin_password' => 'e10adc3949ba59abbe56e057f20f883e', // password
    ];
});
$factory->afterCreating(Admin::class, function($admin,$faker){
	$roles = Roles::where('name','user')->get();
	$admin->roles()->sync($roles->pluck('id_roles')->toArray());
});

