<?php

namespace App\Infrastructure\Repositories\Eloquent;

use App\Domain\Entities\Comment;
use App\Domain\ValueObjects\CommentContent;
use App\Infrastructure\Repositories\Contracts\CommentRepositoryInterface;
use App\Models\Comment as EloquentComment;
use Illuminate\Support\Collection;

class EloquentCommentRepository implements CommentRepositoryInterface
{
    public function findById(int $id): ?Comment
    {
        $eloquentComment = EloquentComment::with(['user', 'post'])->find($id);
        if (!$eloquentComment) {
            return null;
        }

        return $this->toDomainEntity($eloquentComment);
    }

    public function findByPostId(int $postId): Collection
    {
        $eloquentComments = EloquentComment::with(['user'])
            ->where('post_id', $postId)
            ->orderBy('created_at', 'asc')
            ->get();

        return $eloquentComments->map(function ($eloquentComment) {
            return $this->toDomainEntity($eloquentComment);
        });
    }

    public function save(Comment $comment): Comment
    {
        $eloquentComment = new EloquentComment([
            'content' => $comment->getContent()->getValue(),
            'user_id' => $comment->getAuthor()->id,
            'post_id' => $comment->getPostId(),
        ]);

        $eloquentComment->save();

        $eloquentComment->load(['user', 'post']);

        return $this->toDomainEntity($eloquentComment);
    }

    public function update(Comment $comment): Comment
    {
        $eloquentComment = EloquentComment::findOrFail($comment->getId());
        $eloquentComment->update([
            'content' => $comment->getContent()->getValue(),
        ]);

        $eloquentComment->load(['user', 'post']);

        return $this->toDomainEntity($eloquentComment);
    }

    public function delete(int $id): bool
    {
        return EloquentComment::destroy($id) > 0;
    }

    public function toDomainEntity(EloquentComment $eloquentComment): Comment
    {
        return new Comment(
            $eloquentComment->id,
            new CommentContent($eloquentComment->content),
            $eloquentComment->user,
            $eloquentComment->post_id,
            new \DateTime($eloquentComment->created_at),
            new \DateTime($eloquentComment->updated_at)
        );
    }
}