<?php

namespace App\Repositories\Interfaces;

interface CommentInterface
{
    public function getByPost($postId);
    public function store($data);
}
