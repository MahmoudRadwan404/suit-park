<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $pagenation = $request->input('pagination') || 5;
            $rooms = Room::with(['thumbnails', 'amenities'])->paginate($pagenation);

            $rooms->getCollection()->transform(function ($room) {
                $room->amenities->each(fn($amenity) => $amenity->makeHidden('details'));
                return $room;
            });

            return response()->json($rooms);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to fetch rooms', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name_ar' => 'required|string|max:100',
                'name_en' => 'required|string|max:100',
                'stars' => 'required|integer|min:1|max:5',
                'location_ar' => 'required|string|max:200',
                'location_en' => 'required|string|max:200',
                'description_ar' => 'required|string',
                'description_en' => 'required|string',
                'price' => 'required|integer|min:0',
                'type_id' => 'required|integer',
                'type_name_ar' => 'required|string',
                'type_name_en' => 'required|string',
                'wehda_name_ar' => 'required|string|max:150',
                'wehda_name_en' => 'required|string|max:150',
                'area' => 'required|numeric|min:0',
                'look_ar' => 'required|string|max:100',
                'look_en' => 'required|string|max:100',
            ]);

            $room = Room::create($validated);
            return response()->json($room, 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to create room', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $room = Room::with(['images', 'amenities.image'])->findOrFail($id);
            return response()->json($room);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Room not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to fetch room', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name_ar' => 'sometimes|string|max:100',
                'name_en' => 'sometimes|string|max:100',
                'stars' => 'sometimes|integer|min:1|max:5',
                'location_ar' => 'sometimes|string|max:200',
                'location_en' => 'sometimes|string|max:200',
                'description_ar' => 'sometimes|string',
                'description_en' => 'sometimes|string',
                'price' => 'sometimes|integer|min:0',
                'type_id' => 'sometimes|integer',
                'type_name_ar' => 'sometimes|string',
                'type_name_en' => 'sometimes|string',
                'wehda_name_ar' => 'sometimes|string|max:100',
                'wehda_name_en' => 'sometimes|string|max:100',
                'area' => 'sometimes|numeric|min:0',
                'look_ar' => 'sometimes|string|max:100',
                'look_en' => 'sometimes|string|max:100',
            ]);
            $room = Room::findOrFail($id);

            $room->update($validated);
            return response()->json($room);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to update room', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();
            return response()->json(['message' => 'Room deleted successfully']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Room not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to delete room', 'error' => $e->getMessage()], 500);
        }
    }
    public function attachAmenities(Request $request, int $id): JsonResponse
    {
        try {
            $room = Room::findOrFail($id);

            $validated = $request->validate([
                'amenities' => 'required|array',
                'amenities.*.id' => 'required|exists:amenities,id',
                'amenities.*.number' => 'nullable|integer',
                'amenities.*.value' => 'nullable|string',
            ]);

            $syncData = collect($validated['amenities'])->mapWithKeys(fn($amenity) => [
                $amenity['id'] => [
                    'number' => $amenity['number'] ?? null,
                    'value' => $amenity['value'] ?? null,
                ]
            ]);

            $room->amenities()->attach($syncData);

            return response()->json([
                'message' => 'Amenities attached successfully',
                'room' => $room->load('amenities'),
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Room not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to attach amenities', 'error' => $e->getMessage()], 500);
        }
    }

    public function detachAmenities(Request $request, int $id): JsonResponse
    {
        try {
            $room = Room::findOrFail($id);

            $validated = $request->validate([
                'amenity_ids' => 'required|array',
                'amenity_ids.*' => 'exists:amenities,id',
            ]);

            $room->amenities()->detach($validated['amenity_ids']);

            return response()->json([
                'message' => 'Amenities detached successfully',
                'room' => $room->load('amenities'),
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Room not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to detach amenities', 'error' => $e->getMessage()], 500);
        }
    }
    public function filterByType(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type_name_ar' => 'sometimes|string',
                'type_name_en' => 'sometimes|string',
            ]);

            $rooms = Room::with(['images', 'amenities'])
                ->when(isset($validated['type_name_ar']), fn($q) => $q->where('type_name_ar', $validated['type_name_ar']))
                ->when(isset($validated['type_name_en']), fn($q) => $q->where('type_name_en', $validated['type_name_en']))
                ->get();

            return response()->json($rooms);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to filter rooms', 'error' => $e->getMessage()], 500);
        }
    }
}