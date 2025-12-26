<?php

namespace App\Domain\ValueObjects;

class CommentContent
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Comment content cannot be empty');
        }
        if (strlen($value) > 1000) {
            throw new \InvalidArgumentException('Comment content cannot exceed 1000 characters');
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