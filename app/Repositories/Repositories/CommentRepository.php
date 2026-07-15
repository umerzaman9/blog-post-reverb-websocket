<?php

namespace App\Repositories\Repositories;

use App\Events\CommentPosted;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Repositories\Interfaces\CommentInterface;
use Exception;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CommentRepository implements CommentInterface
{
    /**
     * Get all comments belonging to a post.
     *
     * @param int $postId
     * @return mixed
     */
    public function getByPost(int $postId)
    {
        try {
            $comments = Comment::with('user')->where('postId', $postId)->latest()->get();

            return response()->json([
                'response' => [
                    'status' => true,
                    'data'   => [
                        'comments' => CommentResource::collection($comments),
                    ],
                ],
            ], JsonResponse::HTTP_OK);
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Store a new comment and broadcast it in real time.
     *
     * @param int $userId, int $postId, array $data
     * @return mixed
     */
    public function store(int $userId, int $postId, array $data)
    {
        try {
            DB::beginTransaction();

            $comment = Comment::create([
                'postId' => $postId,
                'userId' => $userId,
                'body'   => $data['body'],
            ]);

            broadcast(new CommentPosted($comment))->toOthers();
            Log::info('Broadcast sent successfully!', ['commentId' => $comment->id, 'comment' => $comment]);

            DB::commit();

            return response()->json([
                'response' => [
                    'status' => true,
                    'data'   => [
                        'comment' => new CommentResource($comment),
                    ],
                ],
            ], JsonResponse::HTTP_CREATED);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Broadcast failed: ', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
