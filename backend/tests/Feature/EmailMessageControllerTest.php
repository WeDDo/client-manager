<?php

namespace Tests\Feature;

use App\Services\AuthService;
use App\Services\EmailInboxSettingService;
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
    $this->actingAs($this->user);

    $this->requestData = [
        'name' => 'setting',
    ];

    $this->apiUrl = '/api/email-messages';
});

afterEach(function () {
    DB::rollBack();
});

test('index', function () {
    $response = $this->get($this->apiUrl);
    $response->assertStatus(200);
});
