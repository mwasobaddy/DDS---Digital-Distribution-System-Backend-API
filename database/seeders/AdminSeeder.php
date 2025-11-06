<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'DDS Admin',
            'email' => 'kelvinramsiel@gmail.com',
            'phone' => '+254700000000',
            'default_referral_code' => 'ADMIN_DEFAULT_001',
            'is_active' => true,
            'notification_settings' => [
                'new_registrations' => true,
                'campaign_submissions' => true,
                'system_alerts' => true,
                'daily_digest' => true,
                'monthly_reports' => true,
            ],
        ]);
    }
}
