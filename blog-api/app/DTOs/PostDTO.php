<?php

namespace App\DTOs;

use App\Domain\Entities\Post;
use Illuminate\Support\Collection;

class PostDTO
{
    public int $id;
    public string $title;
    public string $content;
    public ?string $image;
    public array $author;
    public string $created_at;
    public string $updated_at;
    public array $comments; // Array of CommentDTO

    public function __construct(Post $post)
    {
        $this->id = $post->getId();
        $this->title = $post->getTitle()->getValue();
        $this->content = $post->getContent()->getValue();
        $this->image = $post->getImagePath();
        $this->author = [
            'id' => $post->getAuthor()->id,
            'name' => $post->getAuthor()->name,
            'email' => $post->getAuthor()->email,
        ];
        $this->created_at = $post->getCreatedAt()->format('Y-m-d H:i:s');
        $this->updated_at = $post->getUpdatedAt()->format('Y-m-d H:i:s');
        $this->comments = $post->getComments()->map(function ($comment) {
            return new CommentDTO($comment);
        })->toArray();
    }

    public static function fromEntity(Post $post): self
    {
        return new self($post);
    }

    public static function fromCollection(Collection $posts): array
    {
        return $posts->map(function ($post) {
            return self::fromEntity($post);
        })->toArray();
    }
}