document.addEventListener('DOMContentLoaded', function () {
    const postEl = document.getElementById('post-data');
    if (!postEl) return;

    const postId = postEl.dataset.postId;
    const commentsEl = document.getElementById('comments');
    const commentForm = document.getElementById('comment-form');

    function renderComment(c) {
        const div = document.createElement('div');
        div.className = 'card';
        div.innerHTML = `
            <div class="card-body py-2">
                <div class="d-flex justify-content-between">
                    <strong>${c.author}</strong>
                    <span class="text-muted small">${c.createdAt}</span>
                </div>
                <p class="mb-0 mt-1">${c.body}</p>
            </div>
        `;
        commentsEl.prepend(div);
    }

    // Load existing comments — works for guests too
    fetch(`/posts/${postId}/comments`)
        .then(res => res.json())
        .then(res => res.response.data.comments.forEach(renderComment));

    // Listen for new comments in real time — public channel, guests included
    window.Echo.channel(`posts.${postId}`)
        .listen('.comment.posted', (e) => renderComment(e));

    // Only authenticated users have the comment form present in the DOM
    if (commentForm) {
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const body = document.getElementById('comment-body').value;

            fetch(`/posts/${postId}/comments`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Socket-Id': window.Echo.socketId(), // to prevent seeing duplicate comment of sender
                },
                body: JSON.stringify({ body }),
            })
                .then(res => res.json())
                .then(res => {
                    renderComment(res.response.data.comment);
                    document.getElementById('comment-body').value = '';
                });
        });
    }
});