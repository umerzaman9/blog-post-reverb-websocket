<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $post->title }}</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- Vite (Echo / bootstrap.js / comments.js) -->
    @vite(['resources/js/app.js'])
</head>

<body class="bg-light">

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
        <div class="row justify-content-center">
            <div class="col-md-8">

                @auth
                <div class="mb-3">
                    <span class="text-muted small">Currently viewing:</span>
                    <span id="viewers"></span>
                </div>
                @endauth

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h1 class="card-title h3">{{ $post->title }}</h1>
                        <p class="card-text">{{ $post->body }}</p>
                    </div>
                </div>

                @auth
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <form id="comment-form">
                            @csrf
                            <div class="mb-2">
                                <textarea id="comment-body" class="form-control" rows="3"
                                    placeholder="Write a comment..."></textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Post Comment</button>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <p class="text-muted mb-4">
                    <a href="{{ route('login') }}">Login</a> to leave a comment.
                </p>
                @endauth

                <div id="post-data" data-post-id="{{ $post->id }}"></div>

                <h2 class="h5 mb-3">Comments</h2>
                <div id="comments" class="d-flex flex-column gap-2 mb-3"></div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>