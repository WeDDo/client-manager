<?php

namespace Tests\Feature;

use App\Services\AuthService;
use App\Services\ContactService;
use App\Services\EmailInboxSettingService;
use App\Services\Tests\TestService;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::beginTransaction();

    $this->testService = new TestService();
    $this->actingAs($this->testService->getUser());

    $this->requestData = [];

    $this->item = null;
    $this->apiUrl = '/api/contacts';
});

afterEach(function () {
    DB::rollBack();
});

test('index', function () {
    $response = $this->get($this->apiUrl);
    $response->assertStatus(200);
});
