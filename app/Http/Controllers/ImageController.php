<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $pagenation = $request->input('pagination') || 5;

            $images = Image::paginate($pagenation);
            return response()->json($images);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to fetch images', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'room_id' => 'nullable|exists:rooms,id',
                'type' => 'required|string|max:20',
                'images' => 'required|array',
                'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5048',
            ]);

            $uploaded = [];

            foreach ($request->file('images') as $file) {
                $name = $file->getClientOriginalName();
                $path = $file->store('images', 'public');

                $uploaded[] = Image::create([
                    'name' => $name,
                    'path' => asset('storage/' . $path),
                    'type' => $request->type,
                    'room_id' => $request->room_id,
                ]);
            }

            return response()->json([
                'message' => count($uploaded) . ' image(s) uploaded successfully',
                'images' => $uploaded,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to upload images', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $image = Image::with('room')->findOrFail($id);
            return response()->json($image);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Image not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to fetch image', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $image = Image::findOrFail($id);

            $validated = $request->validate([
                'room_id' => 'nullable|exists:rooms,id',
                'type' => 'sometimes|string|max:20',
                'image' => 'sometimes|image|mimes:jpg,jpeg,png,webp|max:5048',
            ]);

            if ($request->hasFile('image')) {
                // Delete old file
                Storage::disk('public')->delete($image->path);

                $file = $request->file('image');
                $validated['name'] = $file->getClientOriginalName();
                $validated['path'] = asset('/storage/' . $file->store('images', 'public'));
            }

            $image->update($validated);
            return response()->json($image);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Image not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to update image', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $image = Image::findOrFail($id);
            Storage::disk('public')->delete($image->path);
            $image->delete();
            return response()->json(['message' => 'Image deleted successfully']);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Image not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to delete image', 'error' => $e->getMessage()], 500);
        }
    }
}