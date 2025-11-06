<?php

namespace Database\Factories;

use App\Models\Scan;
use App\Models\Campaign;
use App\Models\DCD;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Scan>
 */
class ScanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Scan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'qr_code' => fake()->uuid(),
            'dcd_id' => DCD::factory(),
            'campaign_id' => Campaign::factory(),
            'device_id' => fake()->uuid(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'earnings_amount' => fake()->randomFloat(2, 1, 50),
            'earnings_processed' => false,
        ];
    }
}