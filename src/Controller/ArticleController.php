<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\NotFoundException;

final class ArticleController extends Controller
{
    public function show(int $id): string
    {
        $this->articles->incrementViews($id);

        $article = $this->articles->find($id);
        if ($article === null) {
            throw new NotFoundException("Article #{$id} not found");
        }

        $article->categories = $this->categories->forArticle($id);
        $categoryIds = array_map(
            static fn($category) => $category->id,
            $article->categories,
        );

        $similar = $this->articles->similar(
            $id,
            $categoryIds,
            (int) $this->config['similar_count'],
        );

        return $this->view->render('article.tpl', [
            'page_title' => $article->title,
            'article'    => $article,
            'similar'    => $similar,
        ]);
    }
}
