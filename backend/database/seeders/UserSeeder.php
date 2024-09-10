<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AuthService;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userName = config('app.test_user_name');
        $userEmail = config('app.test_user_email');
        $userPassword = config('app.test_user_password');

        if (!$userName || !$userEmail || !$userPassword) {
            return;
        }

        if (!User::where('email', $userEmail)->first()) {
            (new AuthService())->registration([
                'name' => $userName,
                'email' => $userEmail,
                'password' => $userPassword,
            ]);
        }
    }
}
