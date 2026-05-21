<?php

declare(strict_types=1);

namespace App\Model;

final class Article
{
    public array $categories = [];

    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly string $body,
        public readonly ?string $image,
        public readonly int $views,
        public readonly string $publishedAt,
    ) {
    }

    public static function fromRow(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            title:       (string) $row['title'],
            description: isset($row['description']) ? (string) $row['description'] : null,
            body:        (string) ($row['body'] ?? ''),
            image:       isset($row['image']) ? (string) $row['image'] : null,
            views:       (int) $row['views'],
            publishedAt: (string) $row['published_at'],
        );
    }
}
