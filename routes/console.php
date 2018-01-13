<?php

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('make:user {name} {username} {password}', function ($name, $username, $password) {
    $new_user = \App\User::create([
        "name" => $name,
        "username" => $username,
        "password" => bcrypt($password)
    ]);

    echo sprintf("Created: %s\n", $new_user);
})->describe('Create a user account');
