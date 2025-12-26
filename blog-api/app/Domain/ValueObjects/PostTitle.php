<?php

namespace App\Domain\ValueObjects;

class PostTitle
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Post title cannot be empty');
        }
        if (strlen($value) > 255) {
            throw new \InvalidArgumentException('Post title cannot exceed 255 characters');
        }
        $this->value = trim($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}