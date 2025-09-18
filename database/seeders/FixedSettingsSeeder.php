<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting; // Import the Setting model

class FixedSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fixedSettings = [
            'slack_webhook_url' => 'YOUR_SLACK_WEBHOOK_URL_HERE', // Replace with a dummy or actual URL
            'discord_webhook_url' => 'YOUR_DISCORD_WEBHOOK_URL_HERE', // Replace with a dummy or actual URL
            // Add other fixed settings here
            'app_name' => 'Server Monitor',
            'admin_email_for_alerts' => 'admin@example.com',
        ];

        foreach ($fixedSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        $this->command->info('Fixed settings seeded successfully.');
    }
}
