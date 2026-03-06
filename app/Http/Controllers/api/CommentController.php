<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(18);

        return response()->json($comments);
    }

    public function show(Comment $comment)
    {
        $comment->load('user');

        return response()->json($comment);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'content' => 'required|string|max:255',
            'song_id' => 'required|exists:songs,id',
        ]);

        $comment = Comment::create([
            'content' => $validatedData['content'],
            'user_id' => Auth::id(),
            'song_id' => $validatedData['song_id'],
        ]);

        return response()->json($comment->load('user'), 201);
    }

    public function destroy(Comment $comment)
    {
        if ($comment->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully',
        ], 200);
    }

    public function like(Comment $comment)
    {
        try {
            $this->handleReaction($comment, 1);
            $comment->updateReactionCounters();

            return response()->json([
                'success' => true,
                'comment' => $comment,
                'likesCount' => $comment->likesCount,
                'dislikesCount' => $comment->dislikesCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th]);
        }
    }

    public function dislike(Comment $comment)
    {
        try {
            $this->handleReaction($comment, -1);
            $comment->updateReactionCounters();

            return response()->json([
                'success' => true,
                'comment' => $comment,
                'likesCount' => $comment->likesCount,
                'dislikesCount' => $comment->dislikesCount,
            ]);
        } catch (\Throwable $th) {
            return response()->json(['success' => false, 'error' => $th]);
        }
    }

    private function handleReaction(Comment $comment, $type)
    {
        $user = Auth::user();

        // Usar la relación pivot para manejar la reacción
        $existing = $comment->reactions()->where('user_id', $user->id)->first();

        if ($existing) {
            if ($existing->pivot->type === $type) {
                // Toggle off
                $comment->reactions()->detach($user->id);
            } else {
                // Update type
                $comment->reactions()->updateExistingPivot($user->id, ['type' => $type]);
            }
        } else {
            // New reaction
            $comment->reactions()->attach($user->id, ['type' => $type]);
        }
    }

    public function update(Request $request, Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate(['content' => 'required|string|max:255']);
        $comment->update(['content' => $request->content]);

        return response()->json($comment);
    }

    public function reply(Request $request, Comment $comment)
    {
        $request->validate(['content' => 'required|string']);

        $reply = Comment::create([
            'content' => $request->content,
            'user_id' => Auth::id(),
            'parent_id' => $comment->id,
            'song_id' => $comment->song_id,
        ]);

        return response()->json([
            'success' => true,
            'reply' => $reply->load('user'),
        ], 201);
    }
}
