<?php

namespace Tests\Feature;

use App\Services\AuthService;
use App\Services\Tests\TestService;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::beginTransaction();

    $this->testService = new TestService();
    $this->actingAs($this->testService->getUser());

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
