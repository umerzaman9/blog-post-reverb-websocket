<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Repositories\Interfaces\CommentInterface;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $commentRepo;

    public function __construct(CommentInterface $commentRepo)
    {
        $this->commentRepo = $commentRepo;
    }

    /**
     * Return comments belonging to a specific post — public, guests included.
     *
     * @param int $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(int $post)
    {
        return $this->commentRepo->getByPost($post);
    }

    /**
     * Store a new comment for the authenticated user.
     *
     * @param \App\Http\Requests\StoreCommentRequest $request
     * @param int $post
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreCommentRequest $request, int $post)
    {
        return $this->commentRepo->store(auth()->id(), $post, $request->validated());
    }
}
