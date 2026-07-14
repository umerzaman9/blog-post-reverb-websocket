<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\CommentInterface;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $commentRepo;
    public function __construct(CommentInterface $commentRepo)
    {
        $this->commentRepo = $commentRepo;
    }

    // GET /api/posts/{post}/comments — public, guests included
    public function index(int $post)
    {
        return $this->commentRepo->getByPost($post);
    }

    // POST /api/posts/{post}/comments — auth required
    public function store(Request $request, int $post)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        return $this->commentRepo->store([
            'postId' => $post,
            'userId' => $request->user()->id,
            'body'    => $validated['body'],
        ]);
    }
}
