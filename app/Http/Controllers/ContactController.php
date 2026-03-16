<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Throwable;

class ContactController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $pagenation = $request->input('pagination') ?? 5;

            $contacts = Contact::paginate($pagenation);
            return response()->json($contacts);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to fetch contacts', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email',
                'subject' => 'required|string|max:200',
                'message' => 'required|string',
                'type' => 'required|string|max:50',
            ]);

            $contact = Contact::create($validated);
            return response()->json($contact, 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to create contact', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact['status'] = 'opened';
            return response()->json($contact);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Contact not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to fetch contact', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $contact = Contact::findOrFail($id);
            $contact->delete();
            return response()->json(['message' => 'Contact deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Contact not found'], 404);
        } catch (Throwable $e) {
            return response()->json(['message' => 'Failed to delete contact', 'error' => $e->getMessage()], 500);
        }
    }
}