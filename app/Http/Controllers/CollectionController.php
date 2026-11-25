<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Media;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index(User $user): UserResource
    {
        $user->load('media');

        return new UserResource($user);
    }

    public function store(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'media_id' => ['required', 'integer', 'exists:media,id'],
        ]);

        if ($user->media()->where('media_id', $validated['media_id'])->exists()) {
            return response()->json([
                'message' => 'Media sudah ada di koleksi.',
            ], 409);
        }

        $user->media()->attach($validated['media_id']);
        $user->load('media');

        return response()->json([
            'message' => 'Media berhasil ditambahkan ke koleksi ' . $user->name . '!',
            'data' => new UserResource($user),
        ], 201);
    }

    public function destroy(User $user, Media $media): JsonResponse
    {
        if (! $user->media()->where('media_id', $media->id)->exists()) {
            return response()->json([
                'message' => 'Media tidak ada di koleksi.',
            ], 404);
        }

        $user->media()->detach($media->id);
        $user->load('media');

        return response()->json([
            'message' => 'Media berhasil dihapus dari koleksi.',
            'data' => new UserResource($user),
        ], 200);
    }
}
