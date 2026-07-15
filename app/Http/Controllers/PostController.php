<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Exception;
use Flasher\Laravel\Facade\Flasher;
use Illuminate\Http\Request;

class PostController extends Controller
{

    /* Display a listing of all posts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $posts = Post::with('user')->withCount('comments')->latest()->get();

        return view('posts.index', compact('posts'));
    }


    /**
     * Display the specified post.
     *
     * @param $postId
     * @return \Illuminate\View\View
     */
    public function showPosts($postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            Flasher::addError('Sorry, that blog post doesnt exist.');
            return redirect('/');
        }
        return view('posts.show', compact('post'));
    }
}
