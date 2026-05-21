<?php

declare(strict_types=1);

use App\Core\Database;

$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';

$config = require $root . '/config/config.php';
$data   = require $root . '/database/data.php';

$pdo = Database::connect($config['db']);

$uploadsDir = $root . '/public/assets/uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0775, true);
}

echo "Seeding database...\n";

$pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach (['article_category', 'articles', 'categories'] as $table) {
    $pdo->exec("TRUNCATE TABLE {$table}");
}
$pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

$categoryStmt = $pdo->prepare(
    'INSERT INTO categories (name, description) VALUES (:name, :description)'
);

$categoryIds = [];
foreach ($data['categories'] as $key => $category) {
    $categoryStmt->execute([
        'name'        => $category['name'],
        'description' => $category['description'],
    ]);
    $categoryIds[$key] = (int) $pdo->lastInsertId();
}
echo '  categories: ' . count($categoryIds) . "\n";

$articleStmt = $pdo->prepare(
    'INSERT INTO articles (title, description, body, image, views, published_at)
     VALUES (:title, :description, :body, :image, :views, :published_at)'
);
$linkStmt = $pdo->prepare(
    'INSERT INTO article_category (article_id, category_id) VALUES (:article_id, :category_id)'
);

$palette = ['#4f46e5', '#0ea5e9', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#14b8a6'];
$publishDate = new DateTimeImmutable('2026-05-20 12:00:00');

$articleCount = 0;
$linkCount    = 0;

foreach (array_values($data['articles']) as $index => $article) {
    $imageName = sprintf('article-%02d.svg', $index + 1);
    $color     = $palette[$index % count($palette)];

    file_put_contents(
        $uploadsDir . '/' . $imageName,
        placeholderSvg($article['title'], $color),
    );

    $publishedAt = $publishDate
        ->modify('-' . ($index * 4) . ' days')
        ->format('Y-m-d H:i:s');

    $articleStmt->execute([
        'title'        => $article['title'],
        'description'  => $article['description'],
        'body'         => $article['body'],
        'image'        => $imageName,
        'views'        => random_int(40, 2400),
        'published_at' => $publishedAt,
    ]);
    $articleId = (int) $pdo->lastInsertId();
    $articleCount++;

    foreach ($article['categories'] as $categoryKey) {
        if (!isset($categoryIds[$categoryKey])) {
            throw new RuntimeException("Unknown category key: {$categoryKey}");
        }
        $linkStmt->execute([
            'article_id'  => $articleId,
            'category_id' => $categoryIds[$categoryKey],
        ]);
        $linkCount++;
    }
}

echo '  articles:   ' . $articleCount . "\n";
echo '  links:      ' . $linkCount . "\n";
echo "Done.\n";

function placeholderSvg(string $title, string $color): string
{
    $label = htmlspecialchars($title, ENT_QUOTES | ENT_XML1, 'UTF-8');

    return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 800 450">
          <defs>
            <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0" stop-color="{$color}"/>
              <stop offset="1" stop-color="#1f2933"/>
            </linearGradient>
          </defs>
          <rect width="800" height="450" fill="url(#bg)"/>
          <text x="48" y="96" font-family="Arial, Helvetica, sans-serif" font-size="22"
                fill="#ffffff" fill-opacity="0.55">PHP Blog</text>
          <text x="48" y="396" font-family="Arial, Helvetica, sans-serif" font-size="26"
                font-weight="bold" fill="#ffffff">{$label}</text>
        </svg>
        SVG;
}
