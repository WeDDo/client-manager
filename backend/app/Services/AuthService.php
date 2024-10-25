<?php

namespace App\Services;

use App\Models\EmailInboxSetting;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthService
{
    public function registration(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = bcrypt($data['password']);

            $user = User::create($data);
//            $user->profile()->create([]);
            $token = $user->createToken('authToken')->plainTextToken;

//        event(new Registered($user));
            return [
                'item' => $user,
//                'relations' => $user->getAllRelations(),
//                'additional' => $user->getAdditionalData(),
                'token' => $token,
            ];
        });
    }

    public function login(array $data): array
    {
        if (!Auth::attempt($data)) {
            throw new AuthenticationException('Incorrect password');
        }

        $user = auth()->user();
//            ->load('profile');
        $token = $user->createToken('authToken')->plainTextToken;

        EmailInboxSetting::firstOrCreate(
            ['name' => 'INBOX', 'user_id' => $user->id],
            ['name' => 'INBOX']
        );

//        (new SettingService())->createInitialSettings();

        return [
            'item' => $user,
//            'relations' => $user->getAllRelations(),
//            'additional' => $user->getAdditionalData(),
            'token' => $token,
            'settings' => (new SettingService())->getSettings(),
        ];
    }

}


