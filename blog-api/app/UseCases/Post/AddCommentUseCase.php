<?php

namespace App\UseCases\Post;

use App\Commands\AddCommentCommand;
use App\Domain\Entities\Comment;
use App\Infrastructure\Repositories\Contracts\CommentRepositoryInterface;
use App\Infrastructure\Repositories\Contracts\PostRepositoryInterface;
use App\Models\User;

class AddCommentUseCase
{
    private CommentRepositoryInterface $commentRepository;
    private PostRepositoryInterface $postRepository;

    public function __construct(
        CommentRepositoryInterface $commentRepository,
        PostRepositoryInterface $postRepository
    ) {
        $this->commentRepository = $commentRepository;
        $this->postRepository = $postRepository;
    }

    public function execute(AddCommentCommand $command): Comment
    {
        // Verify post exists
        $post = $this->postRepository->findById($command->getPostId());
        if (!$post) {
            throw new \Exception('Post not found');
        }

        $author = User::findOrFail($command->getAuthorId());

        $comment = new Comment(
            0, // ID will be set by DB
            $command->getContent(),
            $author,
            $command->getPostId(),
            new \DateTime(),
            new \DateTime()
        );

        return $this->commentRepository->save($comment);
    }
}