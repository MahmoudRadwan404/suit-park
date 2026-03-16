<?php

namespace App\Http\Controllers;

use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class AmenityController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $amenities = Amenity::with('image')->get();
            return response()->json($amenities);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to fetch amenities', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name_ar' => 'required|string|max:100',
                'name_en' => 'required|string|max:100',
                'image_id' => 'nullable|exists:images,id',

            ]);

            $amenity = Amenity::create($validated);
            return response()->json($amenity->load('image'), 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to create amenity', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $amenity = Amenity::with(['image'])->findOrFail($id);
            return response()->json($amenity);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Amenity not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to fetch amenity', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $amenity = Amenity::findOrFail($id);

            $validated = $request->validate([
                'name_ar' => 'sometimes|string|max:100',
                'name_en' => 'sometimes|string|max:100',
                'image_id' => 'nullable|exists:images,id',
                'number' => 'sometimes|integer',
                'value' => 'sometimes|string',
            ]);

            $amenity->update($validated);
            return response()->json($amenity->load('image'));

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Amenity not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to update amenity', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $amenity = Amenity::findOrFail($id);
            $amenity->delete();
            return response()->json(['message' => 'Amenity deleted successfully']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Amenity not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to delete amenity', 'error' => $e->getMessage()], 500);
        }
    }

    // Attach amenity to a room

}