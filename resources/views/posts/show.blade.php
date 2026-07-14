<x-app-layout>
    <div class="max-w-2xl mx-auto py-8">
        <h1 class="text-2xl font-bold">{{ $post->title }}</h1>
        <p class="mt-2">{{ $post->body }}</p>

        <h2 class="text-xl font-semibold mt-8">Comments</h2>
        <div id="comments" class="space-y-3 mt-4"></div>

        @auth
        <form id="comment-form" class="mt-6">
            @csrf
            <textarea id="comment-body" class="w-full border rounded p-2" rows="3"
                placeholder="Write a comment..."></textarea>
            <button type="submit" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded">Post Comment</button>
        </form>
        @else
        <p class="mt-6 text-gray-500">
            <a href="{{ route('login') }}" class="underline">Login</a> to leave a comment.
        </p>
        @endauth
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    const postId = {{ $post->id }};
    const commentsEl = document.getElementById('comments');

    function renderComment(c) {
        const div = document.createElement('div');
        div.className = 'border rounded p-3';
        div.innerHTML = `<strong>${c.author}</strong> <span class="text-sm text-gray-400">${c.created_at}</span><p>${c.body}</p>`;
        commentsEl.prepend(div);
    }

    // Load existing comments — works for guests too
    fetch(`/posts/${postId}/comments`)
        .then(res => res.json())
        .then(res => res.data.forEach(renderComment));

    // Listen for new comments in real time — public channel, guests included
    window.Echo.channel(`posts.${postId}`)
        .listen('.comment.posted', (e) => renderComment(e));

    @auth
    document.getElementById('comment-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const body = document.getElementById('comment-body').value;

        fetch(`/posts/${postId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ body }),
        })
        .then(res => res.json())
        .then(res => {
            renderComment(res.data);
            document.getElementById('comment-body').value = '';
        });
    });
    @endauth
});
    </script>
</x-app-layout>