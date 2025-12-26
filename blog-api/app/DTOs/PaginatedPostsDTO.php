<?php

namespace App\DTOs;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginatedPostsDTO
{
    public array $data; // Array of PostDTO
    public array $meta;

    public function __construct(LengthAwarePaginator $paginator)
    {
        $this->data = PostDTO::fromCollection(collect($paginator->items()));
        $this->meta = [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ];
    }

    public static function fromPaginator(LengthAwarePaginator $paginator): self
    {
        return new self($paginator);
    }
}