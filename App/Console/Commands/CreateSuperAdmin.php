<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the super admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $superadmin = User::where('email', 'superadmin@bukskwela.com')->first();

        if (!$superadmin) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@bukskwela.com',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
            ]);
            $this->info('Super Admin created successfully!');
        } else {
            $this->info('Super Admin already exists!');
        }
    }
}
