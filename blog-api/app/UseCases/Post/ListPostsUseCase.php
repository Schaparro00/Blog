<?php

namespace App\UseCases\Post;

use App\Infrastructure\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ListPostsUseCase
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function execute(int $perPage = 10): LengthAwarePaginator
    {
        return $this->postRepository->findAllPaginated($perPage);
    }
}