<?php

namespace Database\Factories;

use App\Models\DCD;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DCDFactory extends Factory
{
    protected $model = DCD::class;

    public function definition()
    {
        return [
            'id' => 'DCD_' . strtoupper($this->faker->unique()->lexify('???')),
            'user_id' => User::factory(),
            'referral_code' => 'DCD_' . strtoupper($this->faker->unique()->lexify('??????')),
            'qr_code' => 'DCD_' . strtoupper($this->faker->unique()->lexify('???')) . '_CAMP_PENDING',
            'referring_da_id' => null, // Can be set in test
            'status' => 'active',
        ];
    }
}