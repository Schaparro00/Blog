<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Post\CreatePostUseCase;
use App\UseCases\Post\ListPostsUseCase;
use App\Commands\CreatePostCommand;
use App\DTOs\PostDTO;
use App\DTOs\PaginatedPostsDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    private CreatePostUseCase $createPostUseCase;
    private ListPostsUseCase $listPostsUseCase;

    public function __construct(
        CreatePostUseCase $createPostUseCase,
        ListPostsUseCase $listPostsUseCase
    ) {
        $this->createPostUseCase = $createPostUseCase;
        $this->listPostsUseCase = $listPostsUseCase;
    }

    public function index(Request $request): JsonResponse
    {
        \Log::info('PostController index called', ['user' => $request->user()?->id, 'params' => $request->all()]);
        $perPage = $request->get('per_page', 10);
        $paginator = $this->listPostsUseCase->execute($perPage);

        $dto = PaginatedPostsDTO::fromPaginator($paginator);
        \Log::info('PostController index response', ['posts_count' => count($dto->data), 'meta' => $dto->meta]);

        return response()->json($dto);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'max:2048'], // 2MB max
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $command = new CreatePostCommand(
            $data['title'],
            $data['content'],
            $imagePath,
            Auth::id()
        );

        $post = $this->createPostUseCase->execute($command);
        $dto = PostDTO::fromEntity($post);

        return response()->json($dto, 201);
    }
}