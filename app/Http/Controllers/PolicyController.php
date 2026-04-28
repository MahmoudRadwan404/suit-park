<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Policy;

class PolicyController extends Controller
{
    // GET policy
    public function index()
    {
        $policy = Policy::first();

        return response()->json([
            'data' => $policy
        ]);
    }

    // POST (create or update)
    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required|array'
        ]);

        $policy = Policy::first();

        if (!$policy) {
            // create new
            $policy = Policy::create([
                'content' => $request->input('content')
            ]);
        } else {
            // update only changed data
            $policy->update([
                'content' => array_merge($policy->content ?? [], $request->input('content'))
            ]);
        }

        return response()->json([
            'message' => 'Policy saved successfully',
            'data' => $policy
        ]);
    }
    public function destroy()
    {
        $policy = Policy::first();

        if (!$policy) {
            return response()->json([
                'message' => 'Policy not found'
            ], 404);
        }

        $policy->delete();

        return response()->json([
            'message' => 'Policy deleted successfully'
        ]);
    }
}
