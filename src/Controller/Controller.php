<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

abstract class Controller
{
    public function __construct(
        protected readonly View $view,
        protected readonly CategoryRepository $categories,
        protected readonly ArticleRepository $articles,
        protected readonly array $config,
    ) {
    }
}
