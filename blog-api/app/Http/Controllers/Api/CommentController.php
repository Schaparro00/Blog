<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UseCases\Post\AddCommentUseCase;
use App\Commands\AddCommentCommand;
use App\DTOs\CommentDTO;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    private AddCommentUseCase $addCommentUseCase;

    public function __construct(AddCommentUseCase $addCommentUseCase)
    {
        $this->addCommentUseCase = $addCommentUseCase;
    }

    public function store(Request $request, int $postId): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $command = new AddCommentCommand(
            $data['content'],
            $postId,
            Auth::id()
        );

        $comment = $this->addCommentUseCase->execute($command);
        $dto = CommentDTO::fromEntity($comment);

        return response()->json($dto, 201);
    }
}