<?php

namespace App\Repositories\Interfaces;

interface CommentInterface
{
    public function getByPost(int $postId);

    public function store(int $userId, int $postId, array $data);
}
