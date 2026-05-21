<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Article;
use PDO;

final class ArticleRepository
{
    private const SORTS = [
        'date'  => 'a.published_at DESC, a.id DESC',
        'views' => 'a.views DESC, a.id DESC',
    ];

    public const DEFAULT_SORT = 'date';

    private const CARD_COLUMNS = 'a.id, a.title, a.description, a.image, a.views, a.published_at';

    public function __construct(private readonly PDO $pdo)
    {
    }

    public static function isValidSort(string $sort): bool
    {
        return isset(self::SORTS[$sort]);
    }

    public function find(int $id): ?Article
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, title, description, body, image, views, published_at
               FROM articles
              WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ? Article::fromRow($row) : null;
    }

    public function latestForCategory(int $categoryId, int $limit): array
    {
        $sql = 'SELECT ' . self::CARD_COLUMNS . '
                  FROM articles a
                  JOIN article_category ac ON ac.article_id = a.id
                 WHERE ac.category_id = :cat
                 ORDER BY a.published_at DESC, a.id DESC
                 LIMIT :limit';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('cat', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(Article::fromRow(...), $stmt->fetchAll());
    }

    public function countForCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM article_category WHERE category_id = :cat'
        );
        $stmt->execute(['cat' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }

    public function forCategory(int $categoryId, string $sort, int $limit, int $offset): array
    {
        $orderBy = self::SORTS[$sort] ?? self::SORTS[self::DEFAULT_SORT];

        $sql = 'SELECT ' . self::CARD_COLUMNS . '
                  FROM articles a
                  JOIN article_category ac ON ac.article_id = a.id
                 WHERE ac.category_id = :cat
                 ORDER BY ' . $orderBy . '
                 LIMIT :limit OFFSET :offset';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue('cat', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(Article::fromRow(...), $stmt->fetchAll());
    }

    public function similar(int $excludeId, array $categoryIds, int $limit): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));

        $sql = 'SELECT DISTINCT ' . self::CARD_COLUMNS . '
                  FROM articles a
                  JOIN article_category ac ON ac.article_id = a.id
                 WHERE ac.category_id IN (' . $placeholders . ')
                   AND a.id <> ?
                 ORDER BY a.published_at DESC, a.id DESC
                 LIMIT ?';

        $stmt = $this->pdo->prepare($sql);

        $position = 1;
        foreach ($categoryIds as $categoryId) {
            $stmt->bindValue($position++, (int) $categoryId, PDO::PARAM_INT);
        }
        $stmt->bindValue($position++, $excludeId, PDO::PARAM_INT);
        $stmt->bindValue($position, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return array_map(Article::fromRow(...), $stmt->fetchAll());
    }

    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE articles SET views = views + 1 WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);
    }
}
