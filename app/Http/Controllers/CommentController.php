<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\SongVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::User();

        $validatedData = $request->validate([
            'content' => 'required',
            'song_id' => 'required|exists:songs,id',
        ]);

        $comment = new Comment($validatedData);
        $comment->user_id = $user->id;
        $comment->save();

        return redirect()->back()->with('status', '¡Comentario añadido con éxito!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        //return view('comments.edit', compact('comment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $user = Auth::User();
        // dd(
        //     $request->all(),
        //     $comment,
        //     $user,
        // );

        $validatedData = $request->validate([
            'content' => 'required',
        ]);

        $comment->update($validatedData);

        return redirect()->back()->with('status', '¡Comentario actualizado con éxito!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('status', '¡Comentario eliminado con éxito!');
    }

    public function like(Comment $comment)
    {
        if (Auth::check()) {
            $this->handleReaction($comment, 1); // 1 para like
            $comment->updateReactionCounters(); // Actualiza los contadores manualmente
            return redirect()->back(); // Redirige de vuelta a la página anterior
        }
        return redirect()->route('/')->with('warning', 'Please login');
    }

    public function dislike(Comment $comment)
    {
        if (Auth::check()) {
            $this->handleReaction($comment, -1); // 1 para like
            $comment->updateReactionCounters(); // Actualiza los contadores manualmente
            return redirect()->back(); // Redirige de vuelta a la página anterior
        }
        return redirect()->route('/')->with('warning', 'Please login');
    }

    private function handleReaction(Comment $comment, int $type)
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

    public function reply(Request $request, Comment $comment)
    {
        try {
            $request->validate(['content' => 'required|string']);

            $reply = $comment->replies()->create([
                'content' => $request->content,
                'user_id' => Auth::id(),
                'song_id' => $comment->song_id,
            ]);

            // Notify parent comment author if they are not the one replying
            if ($comment->user_id !== Auth::id()) {
                $comment->user->notifications()->create([
                    'type' => 'reply',
                    'subject_id' => $reply->id,
                    'subject_type' => 'comment',
                    'data' => [
                        'replier_name' => Auth::user()->name,
                        'replier_avatar' => Auth::user()->avatar_url,
                        'comment_content' => Str::limit($reply->content, 50),
                        'message' => Auth::user()->name . ' replied to your comment',
                    ],
                ]);
            }

            return back()->with('success', 'Respuesta enviada.');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
