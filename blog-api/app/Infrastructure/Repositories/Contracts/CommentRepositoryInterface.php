<?php

namespace App\Infrastructure\Repositories\Contracts;

use App\Domain\Entities\Comment;
use Illuminate\Support\Collection;

interface CommentRepositoryInterface
{
    public function findById(int $id): ?Comment;
    public function findByPostId(int $postId): Collection;
    public function save(Comment $comment): Comment;
    public function update(Comment $comment): Comment;
    public function delete(int $id): bool;
}