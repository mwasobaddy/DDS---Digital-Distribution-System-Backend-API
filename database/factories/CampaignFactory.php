<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition()
    {
        return [
            'id' => 'CAMP_' . strtoupper($this->faker->unique()->lexify('??????')),
            'client_id' => User::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'product_url' => $this->faker->url,
            'budget' => $this->faker->randomFloat(2, 1000, 10000),
            'rate_per_scan' => $this->faker->randomFloat(2, 1, 50),
            'status' => 'LIVE',
            'target_counties' => ['Nairobi'],
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ];
    }
}