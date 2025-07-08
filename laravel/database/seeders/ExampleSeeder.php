<?php

namespace Database\Seeders;

use App\Models\Example;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 1; $i <= 50; $i++) {
            Example::create([
                'image' => fake()->imageUrl(640, 480, 'cats', true, 'Faker'),
                'albums' => json_encode([
                    fake()->imageUrl(640, 480, 'nature', true),
                    fake()->imageUrl(640, 480, 'city', true),
                ]),
                'date' => fake()->date(),
                'status' => fake()->randomElement(['draft', 'published', 'archived']),
                'description' => fake()->sentence(12),
                'content' => fake()->paragraph(5),
                'views' => fake()->numberBetween(0, 500),
                'is_active' => fake()->boolean(80), // 80% true
                'price' => fake()->randomFloat(2, 10000, 500000),
                'published' => fake()->numberBetween(1, 3),
                'archive' => fake()->numberBetween(0, 2),
            ]);
        }
    }
}
