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

    $faker = fake();

    $this->requestData = [
        'name' => $faker->name,
        'company_name' => $faker->company,
        'position' => $faker->jobTitle,
        'phone1' => $faker->phoneNumber,
        'phone2' => $faker->phoneNumber,
        'email1' => $faker->safeEmail,
        'email2' => $faker->safeEmail,
        'birthday' => $faker->date('Y-m-d'),
        'notes' => $faker->sentence,
        'address1' => $faker->streetAddress,
        'address2' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => null,
        'postal_code' => $faker->postcode,
        'country' => $faker->country,
        'website' => $faker->url,
        'preferred_contact_method' => null,
        'status' => null,
        'last_contacted_at' => null,
        'partner_id' => null
    ];

    $this->item = (new ContactService())->store($this->requestData);
    $this->apiUrl = '/api/contacts';
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
