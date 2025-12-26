<?php

namespace App\Commands;

use App\Domain\ValueObjects\CommentContent;

class AddCommentCommand
{
    private CommentContent $content;
    private int $postId;
    private int $authorId;

    public function __construct(
        string $content,
        int $postId,
        int $authorId
    ) {
        $this->content = new CommentContent($content);
        $this->postId = $postId;
        $this->authorId = $authorId;
    }

    public function getContent(): CommentContent
    {
        return $this->content;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function getAuthorId(): int
    {
        return $this->authorId;
    }
}