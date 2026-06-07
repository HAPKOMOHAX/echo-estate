<?php

namespace App\Http\Controllers;

use App\Http\Requests\PropertySearchRequest;
use App\Models\Property;
use Illuminate\Http\JsonResponse;

class PropertySearchController extends Controller
{
    public function index(PropertySearchRequest $request): JsonResponse
    {
        $filters = $request->validated();

        $query = Property::query();

        if (! empty($filters['title'])) {
            $title = trim($filters['title']);

            $query->where('title', 'like', "%{$title}%");

            if (($filters['sort'] ?? 'default') === 'default') {
                $exactTitle = $title;
                $titleStartsWithSearch = "{$title}%";
                $titleContainsSearch = "%{$title}%";
                $query->orderByRaw(
                    '
                    CASE
                        WHEN title = ? THEN 0
                        WHEN title LIKE ? THEN 1
                        WHEN title LIKE ? THEN 2
                        ELSE 3
                    END
                    ',
                    [
                        $exactTitle,
                        $titleStartsWithSearch,
                        $titleContainsSearch,
                    ]
                );
            }
        }

        if ($request->boolean('has_photo')) {
            $query->where('has_photo', true);
        }

        if (! empty($filters['rooms'])) {
            $query->whereIn('rooms', $filters['rooms']);
        }

        if (isset($filters['area_from'])) {
            $query->where('area', '>=', $filters['area_from']);
        }

        if (isset($filters['area_to'])) {
            $query->where('area', '<=', $filters['area_to']);
        }

        match ($filters['sort'] ?? 'default') {
            'area_asc' => $query->orderBy('area'),
            'area_desc' => $query->orderByDesc('area'),
            'rooms_asc' => $query->orderBy('rooms'),
            'rooms_desc' => $query->orderByDesc('rooms'),
            'floor_asc' => $query->orderBy('floor'),
            'floor_desc' => $query->orderByDesc('floor'),
            'created_desc' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('id'),
        };

        $properties = $query
            ->paginate($filters['per_page'] ?? 12)
            ->withQueryString()
            ->through(fn (Property $property): array => [
                'id' => $property->id,
                'title' => $property->title,
                'image_path' => $property->image_path,
                'thumbnail_path' => $property->thumbnail_path,
                'has_photo' => $property->has_photo,
                'rooms' => $property->rooms,
                'floor' => $property->floor,
                'area' => $property->area,
                'description' => $property->description,
            ]);

        return response()->json($properties);
    }
}
