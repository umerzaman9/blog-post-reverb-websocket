<?php

namespace App\Repositories\Repositories;

use App\Events\CommentPosted;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentInterface;
use Illuminate\Http\JsonResponse;

class CommentRepository implements CommentInterface
{
    public function getByPost($postId)
    {
        $comments = Comment::with('user:id,name')
            ->where('postId', $postId)
            ->latest()
            ->get()
            ->map(fn($comment) => [
                'id'         => $comment->id,
                'body'       => $comment->body,
                'author'     => $comment->user->name,
                'created_at' => $comment->created_at->diffForHumans(),
            ]);

        return response()->json([
            'success' => true,
            'data'    => $comments,
        ], JsonResponse::HTTP_OK);
    }

    public function store($data)
    {
        $comment = Comment::create([
            'postId' => $data['postId'],
            'userId' => $data['userId'],
            'body'    => $data['body'],
        ]);

        $comment->load('user:id,name');

        broadcast(new CommentPosted($comment))->toOthers();

        return response()->json([
            'success' => true,
            'data' => [
                'id'         => $comment->id,
                'body'       => $comment->body,
                'author'     => $comment->user->name,
                'created_at' => $comment->created_at->diffForHumans(),
            ],
        ], JsonResponse::HTTP_CREATED);
    }
}
