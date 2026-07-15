<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — Blog</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    @vite(['resources/js/app.js'])
</head>

<body class="bg-light mb-3">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">{{ config('app.name') }}</a>
            <div class="d-flex">
                @auth
                <span class="navbar-text text-white me-3">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Log Out</button>
                </form>
                @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-light btn-sm">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="mb-4">All Blogs</h1>

        <div class="row g-4">
            @forelse ($posts as $post)
            <div class="col-md-6">
                <a href="{{ route('posts.show', $post->id) }}" class="text-decoration-none text-dark">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">{{ $post->title }}</h5>
                            <p class="card-text text-muted">{{ Str::limit(strip_tags($post->body), 120) }}</p>
                        </div>
                        <div
                            class="card-footer bg-white border-0 text-muted small d-flex justify-content-between align-items-center">
                            <span>
                                by {{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}
                            </span>

                            <span>
                                <i class="bi bi-chat-dots"></i>
                                {{ $post->comments_count }} comments
                            </span>
                        </div>
                    </div>
                </a>
            </div>
            @empty
            <p class="text-muted">No posts yet.</p>
            @endforelse
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>