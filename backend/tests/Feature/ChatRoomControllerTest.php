<?php

namespace Tests\Feature;

use App\Services\AuthService;
use App\Services\ChatRoomService;
use App\Services\Tests\TestService;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    DB::beginTransaction();

    $this->testService = new TestService();
    $this->actingAs($this->testService->getUser());

    $this->chatRoomData = [
        'name' => 'room1',
        'is_private' => false,
    ];
    $this->chatRoom = (new ChatRoomService())->store($this->chatRoomData);

    $this->apiUrl = '/api/chat-rooms';
});

afterEach(function () {
    DB::rollBack();
});

test('store', function () {
    $response = $this->post($this->apiUrl, $this->chatRoomData);
    $response->assertStatus(200);
});

test('join chat room', function () {
    $response = $this->get("$this->apiUrl/{$this->chatRoom->id}/join");
    $response->assertStatus(200);
});

test('leave chat room', function () {
    $response = $this->get("$this->apiUrl/{$this->chatRoom->id}/leave");
    $response->assertStatus(200);
});
