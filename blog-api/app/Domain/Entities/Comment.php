<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\CommentContent;
use App\Models\User;

class Comment
{
    private int $id;
    private CommentContent $content;
    private User $author;
    private int $postId;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;

    public function __construct(
        int $id,
        CommentContent $content,
        User $author,
        int $postId,
        \DateTime $createdAt,
        \DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->content = $content;
        $this->author = $author;
        $this->postId = $postId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getContent(): CommentContent
    {
        return $this->content;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    // Business methods
    public function updateContent(CommentContent $content): void
    {
        $this->content = $content;
        $this->updatedAt = new \DateTime();
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->author->id === $user->id;
    }
}