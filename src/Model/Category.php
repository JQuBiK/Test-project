<?php

declare(strict_types=1);

namespace App\Model;

final class Category
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            name:        (string) $row['name'],
            description: isset($row['description']) ? (string) $row['description'] : null,
        );
    }
}
