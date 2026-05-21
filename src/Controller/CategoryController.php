<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\NotFoundException;
use App\Repository\ArticleRepository;

final class CategoryController extends Controller
{
    public function show(int $id): string
    {
        $category = $this->categories->find($id);
        if ($category === null) {
            throw new NotFoundException("Category #{$id} not found");
        }

        $sort    = $this->resolveSort();
        $perPage = (int) $this->config['articles_per_page'];

        $total      = $this->articles->countForCategory($id);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page       = $this->resolvePage($totalPages);

        $articles = $this->articles->forCategory(
            $id,
            $sort,
            $perPage,
            ($page - 1) * $perPage,
        );

        return $this->view->render('category.tpl', [
            'page_title'  => $category->name,
            'category'    => $category,
            'articles'    => $articles,
            'total'       => $total,
            'sort'        => $sort,
            'page'        => $page,
            'total_pages' => $totalPages,
            'pages'       => range(1, $totalPages),
        ]);
    }

    private function resolveSort(): string
    {
        $sort = $_GET['sort'] ?? '';

        return is_string($sort) && ArticleRepository::isValidSort($sort)
            ? $sort
            : ArticleRepository::DEFAULT_SORT;
    }

    private function resolvePage(int $totalPages): int
    {
        $page = (int) ($_GET['page'] ?? 1);

        return max(1, min($page, $totalPages));
    }
}
