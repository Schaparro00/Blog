<?php

namespace App\Infrastructure\Repositories\Contracts;

use App\Domain\Entities\Post;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function findById(int $id): ?Post;
    public function findAllPaginated(int $perPage = 10): LengthAwarePaginator;
    public function save(Post $post): Post;
    public function update(Post $post): Post;
    public function delete(int $id): bool;
}