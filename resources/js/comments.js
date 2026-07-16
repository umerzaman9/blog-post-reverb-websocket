document.addEventListener('DOMContentLoaded', function () {

    toastr.options = { positionClass: 'toast-bottom-right' };

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

    // Load existing comments
    $.ajax({
        url: `/posts/${postId}/comments`,
        type: 'GET',
        dataType: 'json',
        success: function (res) {
            res.response.data.comments.reverse().forEach(renderComment);
        },
        error: function (xhr) {
            console.error(xhr);
            toastr.error('Something went wrong');
        }
    });

    // Listen for new comments in real time — public channel, guests included
    window.Echo.channel(`posts.${postId}`)
        .listen('.comment.posted', (e) => renderComment(e));

    //who is viewing post
    const viewersEl = document.getElementById('viewers');
    window.Echo.join(`presence-posts.${postId}`)
        .here((users) => {
            // Fired once, when you join — gives the full current viewers including self
            renderViewers(users);
        })
        .joining((user) => {
            // Fired for everyone already in the channel, when someone new joins
            toastr.info(`${user.name} joined`);
            addViewer(user);
        })
        .leaving((user) => {
            // Fired when someone leaves (closes tab, navigates away)
            toastr.info(`${user.name} left`);
            removeViewer(user);
        })
        .error((error) => {
            console.error('Presence channel error:', error);
        });

    function renderViewers(users) {
        viewersEl.innerHTML = '';
        users.forEach(addViewer);
    }

    function addViewer(user) {
        const span = document.createElement('span');
        span.className = 'badge bg-secondary me-1';
        span.dataset.userId = user.id;
        span.textContent = user.name;
        viewersEl.appendChild(span);
    }

    function removeViewer(user) {
        const el = viewersEl.querySelector(`[data-user-id="${user.id}"]`);
        if (el) el.remove();
    }

    // create a comment
    if (commentForm) {
        commentForm.addEventListener('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: `/posts/${postId}/comments`,
                type: 'POST',
                dataType: 'json',
                data: {
                    body: $('#comment-body').val()
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Socket-Id': window.Echo.socketId(), // to prevent self broadcast
                },
                success: function (res) {
                    renderComment(res.response.data.comment);
                    $('#comment-body').val('');
                    toastr.success('Comment posted successfully!');
                },
                error: function (xhr) {
                    console.error(xhr);
                    toastr.error('Something went wrong');
                }
            });
        });
    }
});