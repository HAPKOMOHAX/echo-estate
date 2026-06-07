<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $images = collect(File::files(database_path('seeders/assets/properties')))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), [
                'jpg',
                'jpeg',
                'png',
                'webp',
            ]))
            ->values();

        $imagePool = $this->createImagePool($images);

        $remainingCount = 10000 - count($imagePool);

        DB::transaction(function () use ($remainingCount, $imagePool): void {
            Property::factory()
                ->count($remainingCount)
                ->state(function () use ($imagePool): array {
                    if (empty($imagePool) || ! fake()->boolean(75)) {
                        return [
                            'has_photo' => false,
                            'image_path' => null,
                            'thumbnail_path' => null,
                        ];
                    }

                    $image = fake()->randomElement($imagePool);

                    return [
                        'has_photo' => true,
                        'image_path' => $image['image_path'],
                        'thumbnail_path' => $image['thumbnail_path'],
                    ];
                })
                ->create();
        });
    }

    private function createImagePool($images): array
    {
        $pool = [];

        foreach ($images as $image) {
            $property = Property::factory()->create([
                'has_photo' => true,
                'image_path' => null,
                'thumbnail_path' => null,
            ]);

            $property
                ->addMedia($image->getPathname())
                ->preservingOriginal()
                ->toMediaCollection('images');

            $imagePath = $property->getFirstMediaUrl('images');
            $thumbnailPath = $property->getFirstMediaUrl('images', 'thumb');

            $property->forceFill([
                'image_path' => $imagePath,
                'thumbnail_path' => $thumbnailPath,
            ])->save();

            $pool[] = [
                'image_path' => $imagePath,
                'thumbnail_path' => $thumbnailPath,
            ];
        }

        return $pool;
    }
}
