<?php

declare(strict_types=1);

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Core\Database;
use App\Core\NotFoundException;
use App\Core\Router;
use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

$root = dirname(__DIR__);

require $root . '/vendor/autoload.php';

$config = require $root . '/config/config.php';

error_reporting(E_ALL);
ini_set('display_errors', $config['app']['debug'] ? '1' : '0');

$view = new View($config['paths']);

try {
    $pdo = Database::connect($config['db']);

    $categories = new CategoryRepository($pdo);
    $articles   = new ArticleRepository($pdo);

    $home     = new HomeController($view, $categories, $articles, $config['app']);
    $category = new CategoryController($view, $categories, $articles, $config['app']);
    $article  = new ArticleController($view, $categories, $articles, $config['app']);

    $router = new Router();
    $router->get('/', static fn (): string => $home->index());
    $router->get('/category/{id}', static fn (array $p): string => $category->show((int) $p['id']));
    $router->get('/article/{id}', static fn (array $p): string => $article->show((int) $p['id']));

    echo $router->dispatch($_SERVER['REQUEST_URI'] ?? '/');
} catch (NotFoundException $e) {
    http_response_code(404);
    echo $view->render('404.tpl', [
        'page_title' => 'Страница не найдена',
        'message'    => $e->getMessage(),
    ]);
} catch (Throwable $e) {
    http_response_code(500);

    if ($config['app']['debug']) {
        echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES) . '</pre>';
    } else {
        echo 'Внутренняя ошибка сервера';
    }
}
