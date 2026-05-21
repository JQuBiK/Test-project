<?php

declare(strict_types=1);

namespace App\Controller;

final class HomeController extends Controller
{
    public function index(): string
    {
        $blocks = [];

        foreach ($this->categories->allWithArticles() as $category) {
            $blocks[] = [
                'category' => $category,
                'articles' => $this->articles->latestForCategory(
                    $category->id,
                    (int) $this->config['latest_per_block'],
                ),
            ];
        }

        return $this->view->render('home.tpl', [
            'page_title' => 'Последние статьи',
            'blocks'     => $blocks,
        ]);
    }
}
