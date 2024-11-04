<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function getSettings(): array
    {
        $settings = Setting::all();

        return $settings->mapWithKeys(function ($setting) {
            $toBoolSettings = [
                'on_create_go_to_edit_form',
            ];

            if (in_array($setting->name, $toBoolSettings)) {
                $setting->value = !!intval($setting->value);
            }

            return [$setting->name => $setting->value];
        })->toArray();
    }

    public function updateAll(array $data): void
    {
        foreach ($data as $name => $value) {
            Setting::updateOrCreate(
                ['name' => $name],
                ['value' => $value]
            );
        }
    }

    public function createInitialSettings(): void
    {
        $settingsData = [
            ['name' => 'on_create_go_to_edit_form', 'value' => true],
        ];

        foreach ($settingsData as $settingData) {
            Setting::firstOrCreate(
                ['name' => $settingData['name']],
                ['value' => $settingData['value']]
            );
        }
    }
}


