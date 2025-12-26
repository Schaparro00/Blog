<?php

namespace App\UseCases\Post;

use App\Commands\CreatePostCommand;
use App\Domain\Entities\Post;
use App\Infrastructure\Repositories\Contracts\PostRepositoryInterface;
use App\Models\User;

class CreatePostUseCase
{
    private PostRepositoryInterface $postRepository;

    public function __construct(PostRepositoryInterface $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function execute(CreatePostCommand $command): Post
    {
        $author = User::findOrFail($command->getAuthorId());

        $post = new Post(
            0, // ID will be set by DB
            $command->getTitle(),
            $command->getContent(),
            $command->getImagePath(),
            $author,
            new \DateTime(),
            new \DateTime()
        );

        return $this->postRepository->save($post);
    }
}