<?php

namespace App\Domain\Entities;

use App\Domain\ValueObjects\PostTitle;
use App\Domain\ValueObjects\PostContent;
use App\Models\User;
use Illuminate\Support\Collection;

class Post
{
    private int $id;
    private PostTitle $title;
    private PostContent $content;
    private ?string $imagePath;
    private User $author;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private Collection $comments; // Collection of Comment entities

    public function __construct(
        int $id,
        PostTitle $title,
        PostContent $content,
        ?string $imagePath,
        User $author,
        \DateTime $createdAt,
        \DateTime $updatedAt,
        Collection $comments = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->imagePath = $imagePath;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->comments = $comments ?? collect();
    }

    // Getters
    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): PostTitle
    {
        return $this->title;
    }

    public function getContent(): PostContent
    {
        return $this->content;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    // Business methods
    public function updateTitle(PostTitle $title): void
    {
        $this->title = $title;
        $this->updatedAt = new \DateTime();
    }

    public function updateContent(PostContent $content): void
    {
        $this->content = $content;
        $this->updatedAt = new \DateTime();
    }

    public function updateImage(?string $imagePath): void
    {
        $this->imagePath = $imagePath;
        $this->updatedAt = new \DateTime();
    }

    public function addComment(Comment $comment): void
    {
        $this->comments->push($comment);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->author->id === $user->id;
    }
}