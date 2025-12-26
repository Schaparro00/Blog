<?php

namespace App\Domain\ValueObjects;

class PostContent
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty(trim($value))) {
            throw new \InvalidArgumentException('Post content cannot be empty');
        }
        if (strlen($value) > 10000) {
            throw new \InvalidArgumentException('Post content cannot exceed 10000 characters');
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