<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name="title"}{$page_title|default:'Блог'|escape}{/block} — PHP Blog</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container site-header__inner">
            <a class="site-logo" href="/">PHP&nbsp;Blog</a>
            <nav class="site-nav">
                <a href="/">Главная</a>
            </nav>
        </div>
    </header>

    <main class="container page">
        {block name="content"}{/block}
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>Test project — учебный блог на чистом PHP, Smarty и MySQL.</p>
        </div>
    </footer>
</body>
</html>
