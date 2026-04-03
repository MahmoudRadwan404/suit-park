<?php

namespace App\Http\Controllers;

use App\Models\NazelName;
use Illuminate\Http\Request;

class NazelController extends Controller
{
    // List all
    public function index()
    {
        $nazels = NazelName::all();

        return response()->json([
            'status' => true,
            'data' => $nazels,
        ]);
    }

    // Show single record
    public function show($id)
    {
        $nazel = NazelName::findOrFail($id);

        if (!$nazel) {
            return response()->json([
                'status' => false,
                'message' => 'Nazel not found',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $nazel,
        ]);
    }

    // Create
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $nazel = NazelName::create([
            'name_ar' => $request->name_ar,
            'name_en' => $request->name_en,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Nazel created successfully',
            'data' => $nazel,
        ], 201);
    }

    // Update
    public function update(Request $request, $id)
    {
        $nazel = NazelName::findOrFail($id);

        $request->validate([
            'name_ar' => 'sometimes|string|max:255',
            'name_en' => 'sometimes|string|max:255',
        ]);

        $nazel->update($request->only(['name_ar', 'name_en']));

        return response()->json([
            'status' => true,
            'message' => 'Nazel updated successfully',
            'data' => $nazel,
        ]);
    }
    // Delete
    public function destroy($id)
    {
        $nazel = NazelName::find($id);

        if (!$nazel) {
            return response()->json([
                'status' => false,
                'message' => 'Nazel not found',
            ], 404);
        }

        $nazel->delete();

        return response()->json([
            'status' => true,
            'message' => 'Nazel deleted successfully',
        ]);
    }

    // ============================================
    // Filter Function (separate from index)
    // ============================================

    public function filter(Request $request)
    {
        $results = NazelName::with(['rooms', 'rooms.thumbnails', 'rooms.amenities'])
            ->when($request->name_ar, fn($q) => $q->where('name_ar', $request->name_ar))
            ->when($request->name_en, fn($q) => $q->orWhere('name_en', $request->name_en))
            ->get();
        return response()->json([
            'status' => true,
            'data' => $results,
        ]);
    }
}