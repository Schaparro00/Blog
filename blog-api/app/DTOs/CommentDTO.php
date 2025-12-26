<?php

namespace App\DTOs;

use App\Domain\Entities\Comment;

class CommentDTO
{
    public int $id;
    public string $content;
    public array $author;
    public int $post_id;
    public string $created_at;
    public string $updated_at;

    public function __construct(Comment $comment)
    {
        $this->id = $comment->getId();
        $this->content = $comment->getContent()->getValue();
        $this->author = [
            'id' => $comment->getAuthor()->id,
            'name' => $comment->getAuthor()->name,
            'email' => $comment->getAuthor()->email,
        ];
        $this->post_id = $comment->getPostId();
        $this->created_at = $comment->getCreatedAt()->format('Y-m-d H:i:s');
        $this->updated_at = $comment->getUpdatedAt()->format('Y-m-d H:i:s');
    }

    public static function fromEntity(Comment $comment): self
    {
        return new self($comment);
    }
}