<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:user {name} {username} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user account for scrap.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $new_user = User::create([
            "name"     => $this->argument("name"),
            "username" => $this->argument("username"),
            "password" => bcrypt($this->argument("password"))
        ]);

        echo sprintf("Created: %s\n", $new_user);
    }
}
