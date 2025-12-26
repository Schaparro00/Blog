<?php

namespace App\Infrastructure\Repositories\Eloquent;

use App\Domain\Entities\Post;
use App\Domain\ValueObjects\PostTitle;
use App\Domain\ValueObjects\PostContent;
use App\Infrastructure\Repositories\Contracts\PostRepositoryInterface;
use App\Models\Post as EloquentPost;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentPostRepository implements PostRepositoryInterface
{
    public function findById(int $id): ?Post
    {
        $eloquentPost = EloquentPost::with(['user', 'comments.user'])->find($id);
        if (!$eloquentPost) {
            return null;
        }

        return $this->toDomainEntity($eloquentPost);
    }

    public function findAllPaginated(int $perPage = 10): LengthAwarePaginator
    {
        $paginator = EloquentPost::with(['user', 'comments.user'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $domainPosts = $paginator->getCollection()->map(function ($eloquentPost) {
            return $this->toDomainEntity($eloquentPost);
        });

        $paginator->setCollection($domainPosts);

        return $paginator;
    }

    public function save(Post $post): Post
    {
        $eloquentPost = new EloquentPost([
            'title' => $post->getTitle()->getValue(),
            'content' => $post->getContent()->getValue(),
            'image_path' => $post->getImagePath(),
            'user_id' => $post->getAuthor()->id,
        ]);

        $eloquentPost->save();

        // Reload with relationships
        $eloquentPost->load(['user', 'comments.user']);

        return $this->toDomainEntity($eloquentPost);
    }

    public function update(Post $post): Post
    {
        $eloquentPost = EloquentPost::findOrFail($post->getId());
        $eloquentPost->update([
            'title' => $post->getTitle()->getValue(),
            'content' => $post->getContent()->getValue(),
            'image_path' => $post->getImagePath(),
        ]);

        $eloquentPost->load(['user', 'comments.user']);

        return $this->toDomainEntity($eloquentPost);
    }

    public function delete(int $id): bool
    {
        return EloquentPost::destroy($id) > 0;
    }

    private function toDomainEntity(EloquentPost $eloquentPost): Post
    {
        $comments = $eloquentPost->comments->map(function ($eloquentComment) {
            return app(EloquentCommentRepository::class)->toDomainEntity($eloquentComment);
        });

        return new Post(
            $eloquentPost->id,
            new PostTitle($eloquentPost->title),
            new PostContent($eloquentPost->content),
            $eloquentPost->image_path,
            $eloquentPost->user,
            new \DateTime($eloquentPost->created_at),
            new \DateTime($eloquentPost->updated_at),
            $comments
        );
    }
}