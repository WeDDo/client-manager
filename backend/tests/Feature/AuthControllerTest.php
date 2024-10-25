<?php

namespace Tests\Feature;

use App\Services\AuthService;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::beginTransaction();

    $data = (new AuthService())->registration([
        'name' => 'test',
        'email' => 'test@test.test',
        'password' => 'test',
        'confirm_password' => 'test',
    ]);

    $this->user = $data['item'];
    $this->apiUrl = '/api/login';
});

afterEach(function () {
    DB::rollBack();
});

test('login', function () {
    $response = $this->post($this->apiUrl, [
        'email' => 'test@test.test',
        'password' => 'test',
    ]);
    $response->assertStatus(200);
});
