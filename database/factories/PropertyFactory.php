<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $hasPhoto = fake()->boolean(75);
        $rooms = fake()->numberBetween(1, 5);

        $title = match ($rooms) {
            1 => fake()->randomElement([
                'Однокомнатная квартира',
                'Уютная однокомнатная квартира',
                'Светлая однокомнатная квартира',
            ]),
            2 => fake()->randomElement([
                'Двухкомнатная квартира',
                'Просторная двухкомнатная квартира',
                'Светлая двухкомнатная квартира',
            ]),
            3 => fake()->randomElement([
                'Трехкомнатная квартира',
                'Просторная трехкомнатная квартира',
                'Семейная трехкомнатная квартира',
            ]),
            4 => fake()->randomElement([
                'Четырехкомнатная квартира',
                'Большая четырехкомнатная квартира',
                'Просторная четырехкомнатная квартира',
            ]),
            default => fake()->randomElement([
                'Многокомнатная квартира',
                'Большая квартира',
                'Просторная квартира',
            ]),
        };

        return [
            'title' => $title,
            'image_path' => null,
            'thumbnail_path' => null,
            'has_photo' => $hasPhoto,
            'rooms' => $rooms,
            'floor' => fake()->numberBetween(1, 25),
            'area' => fake()->randomFloat(2, 18, 180),
            'description' => fake()->paragraphs(2, true),
        ];
    }
}
