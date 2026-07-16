<?php

namespace App\Events;

use App\Models\Comment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentPosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $comment;
    /**
     * Create a new event instance.
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('posts.' . $this->comment->postId),
        ];

        // Don't notify the author about their own comment
        if ($this->comment->post->userId !== $this->comment->userId) {
            $channels[] = new PrivateChannel('users.' . $this->comment->post->userId);
        }

        return $channels;
    }

    public function broadcastAs()
    {
        return 'comment.posted';
    }

    public function broadcastWith(): array
    {
        return [
            'id'         => $this->comment->id,
            'body'       => $this->comment->body,
            'author'     => $this->comment->user->name,
            'created_at' => $this->comment->created_at->diffForHumans(),
            'post_id'    => $this->comment->postId,
            'post_title' => $this->comment->post->title,
        ];
    }
}
