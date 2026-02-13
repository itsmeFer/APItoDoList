<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SecureNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SecureNoteController extends Controller
{
    public function index(Request $request)
    {
        $query = SecureNote::where('user_id', $request->user()->id);

        if ($request->has('type') && $request->type !== 'all') {
            $query->byType($request->type);
        }

        if ($request->has('favorite') && $request->favorite) {
            $query->favorites();
        }

        $notes = $query->latest()->get()->map(function ($note) {
            return [
                'id' => (string) $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'type' => $note->type,
                'icon' => $note->icon,
                'is_favorite' => (bool) $note->is_favorite,
                'created_at' => $note->created_at->toIso8601String(),
                'updated_at' => $note->updated_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Notes retrieved successfully',
            'data' => $notes,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'type' => 'required|in:password,pin,card,note,other',
            'icon' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $note = SecureNote::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->type,
            'icon' => $request->icon,
            'is_favorite' => $request->is_favorite ?? false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Secure note created successfully',
            'data' => [
                'id' => (string) $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'type' => $note->type,
                'icon' => $note->icon,
                'is_favorite' => (bool) $note->is_favorite,
                'created_at' => $note->created_at->toIso8601String(),
                'updated_at' => $note->updated_at->toIso8601String(),
            ],
        ], 201);
    }

    public function show(Request $request, $id)
    {
        $note = SecureNote::where('user_id', $request->user()->id)
                          ->where('id', $id)
                          ->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Note retrieved successfully',
            'data' => [
                'id' => (string) $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'type' => $note->type,
                'icon' => $note->icon,
                'is_favorite' => (bool) $note->is_favorite,
                'created_at' => $note->created_at->toIso8601String(),
                'updated_at' => $note->updated_at->toIso8601String(),
            ],
        ]);
    }

    public function update(Request $request, $id)
    {
        $note = SecureNote::where('user_id', $request->user()->id)
                          ->where('id', $id)
                          ->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'type' => 'sometimes|required|in:password,pin,card,note,other',
            'icon' => 'nullable|string',
            'is_favorite' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $note->update($request->only([
            'title',
            'content',
            'type',
            'icon',
            'is_favorite',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Note updated successfully',
            'data' => [
                'id' => (string) $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'type' => $note->type,
                'icon' => $note->icon,
                'is_favorite' => (bool) $note->is_favorite,
                'created_at' => $note->created_at->toIso8601String(),
                'updated_at' => $note->updated_at->toIso8601String(),
            ],
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $note = SecureNote::where('user_id', $request->user()->id)
                          ->where('id', $id)
                          ->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], 404);
        }

        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Note deleted successfully',
            'data' => [
                'deleted' => true,
                'id' => (string) $id,
            ],
        ]);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $note = SecureNote::where('user_id', $request->user()->id)
                          ->where('id', $id)
                          ->first();

        if (!$note) {
            return response()->json([
                'success' => false,
                'message' => 'Note not found',
            ], 404);
        }

        $note->update(['is_favorite' => !$note->is_favorite]);

        return response()->json([
            'success' => true,
            'message' => 'Favorite toggled successfully',
            'data' => [
                'id' => (string) $note->id,
                'title' => $note->title,
                'content' => $note->content,
                'type' => $note->type,
                'icon' => $note->icon,
                'is_favorite' => (bool) $note->is_favorite,
                'created_at' => $note->created_at->toIso8601String(),
                'updated_at' => $note->updated_at->toIso8601String(),
            ],
        ]);
    }
}