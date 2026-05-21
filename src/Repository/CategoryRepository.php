<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Category;
use PDO;

final class CategoryRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function find(int $id): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, name, description FROM categories WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Category::fromRow($row) : null;
    }

    public function allWithArticles(): array
    {
        $sql = 'SELECT id, name, description
                  FROM categories c
                 WHERE EXISTS (
                       SELECT 1 FROM article_category ac WHERE ac.category_id = c.id
                 )
                 ORDER BY name';

        $rows = $this->pdo->query($sql)->fetchAll();

        return array_map(Category::fromRow(...), $rows);
    }

    public function forArticle(int $articleId): array
    {
        $sql = 'SELECT c.id, c.name, c.description
                  FROM categories c
                  JOIN article_category ac ON ac.category_id = c.id
                 WHERE ac.article_id = :id
                 ORDER BY c.name';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $articleId]);

        return array_map(Category::fromRow(...), $stmt->fetchAll());
    }
}
