<?php

namespace Database\Factories;

use App\Entities\Judger;
use Illuminate\Database\Eloquent\Factories\Factory;

class JudgerFactory extends Factory
{
    protected $model = Judger::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(2),
            'description' => 'test judger',
            'code' => fake()->unique()->uuid(),
            'status' => Judger::ST_ACTIVITY,
            'category' => 0,
        ];
    }
}
