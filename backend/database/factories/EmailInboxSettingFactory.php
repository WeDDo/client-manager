<?php

namespace Database\Factories;

use App\Models\EmailInboxSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmailInboxSetting>
 */
class EmailInboxSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'auto_set_is_seen' => $this->faker->boolean,
        ];
    }
}
