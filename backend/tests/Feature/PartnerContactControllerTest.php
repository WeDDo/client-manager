<?php

namespace Tests\Feature;

use App\Services\AuthService;
use App\Services\EmailInboxSettingService;
use App\Services\PartnerService;
use App\Services\Tests\TestService;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::beginTransaction();

    $this->testService = new TestService();
    $this->actingAs($this->testService->getUser());

    $this->requestData = [];

    $this->item = null;
    $this->apiUrl = '/api/partners';
});

afterEach(function () {
    DB::rollBack();
});

test('index', function () {
    $response = $this->get($this->apiUrl);
    $response->assertStatus(200);
});

