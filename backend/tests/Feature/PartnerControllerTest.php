<?php

namespace Tests\Feature;

use App\Services\AuthService;
use App\Services\EmailInboxSettingService;
use App\Services\PartnerService;
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
        'id_name' => 'TEST_STOCK_' . time(),
        'name' => 'TEST STOCK',
        'name2' => 'TEST STOCK',
        'legal_status' => null,
        'email' => 'abc@gmail.com',
        'phone' => null,
    ];

    $this->item = (new PartnerService())->store($this->requestData);
    $this->apiUrl = '/api/partners';
});

afterEach(function () {
    DB::rollBack();
});

test('index', function () {
    $response = $this->get($this->apiUrl);
    $response->assertStatus(200);
});

test('show', function () {
    $response = $this->get("$this->apiUrl/{$this->item->id}");
    $response->assertStatus(200);
});

test('store', function () {
    $this->requestData['id_name'] = 'TEST_TEST_TEST';
    $response = $this->post($this->apiUrl, $this->requestData);
    $response->assertStatus(200);
});

test('update', function () {
    $response = $this->put("$this->apiUrl/{$this->item->id}", $this->requestData);
    $response->assertStatus(200);
});

test('destroy', function () {
    $response = $this->delete("$this->apiUrl/{$this->item->id}");
    $response->assertStatus(204);
});
