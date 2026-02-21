<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Reaction;
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
            'commentable_id' => $validatedData['song_id'],
            'commentable_type' => Song::class,
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

        $reaction = Reaction::where('user_id', $user->id)
            ->where('reactable_id', $comment->id)
            ->where('reactable_type', Comment::class)
            ->first();

        if ($reaction) {
            if ($reaction->type === $type) {
                $reaction->delete();
            } else {
                $reaction->update(['type' => $type]);
            }
        } else {
            Reaction::create([
                'user_id' => $user->id,
                'reactable_id' => $comment->id,
                'reactable_type' => Comment::class,
                'type' => $type,
            ]);
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
            'commentable_type' => $comment::class,
            'commentable_id' => $comment->id,
        ]);

        return response()->json([
            'success' => true,
            'reply' => $reply->load('user'),
        ], 201);
    }
}
