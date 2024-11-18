<?php

namespace App\Services\Tests;

use App\Models\User;
use App\Services\AuthService;

class TestService
{
    public function getUser(): User
    {
        $data = (new AuthService())->registration([
            'name' => 'test',
            'email' => 'test@test.test',
            'password' => 'test',
            'confirm_password' => 'test',
        ]);

        return $data['item'];
    }
}


