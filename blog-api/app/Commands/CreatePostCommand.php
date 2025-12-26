<?php

namespace App\Commands;

use App\Domain\ValueObjects\PostTitle;
use App\Domain\ValueObjects\PostContent;

class CreatePostCommand
{
    private PostTitle $title;
    private PostContent $content;
    private ?string $imagePath;
    private int $authorId;

    public function __construct(
        string $title,
        string $content,
        ?string $imagePath,
        int $authorId
    ) {
        $this->title = new PostTitle($title);
        $this->content = new PostContent($content);
        $this->imagePath = $imagePath;
        $this->authorId = $authorId;
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

    public function getAuthorId(): int
    {
        return $this->authorId;
    }
}