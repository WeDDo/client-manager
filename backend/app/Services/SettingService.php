<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function getSettings(): array
    {
        $settings = Setting::all();

        return $settings->mapWithKeys(function ($setting) {
            if($setting->value === '0' || $setting->value === '1') {
                $setting->value = !!$setting->value;
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

//    public function createInitialSettings(): void
//    {
//        $settingsData = [
////            ['name' => 'currency', 'value' => '€'],
////            ['name' => 'import_marks_visible', 'value' => false],
////            ['name' => 'calendar_show_day_expenses', 'value' => false],
//        ];
//
//        foreach ($settingsData as $settingData) {
//            Setting::firstOrCreate(
//                ['name' => $settingData['name']],
//                ['value' => $settingData['value']]
//            );
//        }
//    }
}


