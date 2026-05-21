{extends file='layout.tpl'}

{block name="content"}
    <div class="error-page">
        <h1 class="error-page__code">404</h1>
        <p class="error-page__text">{$message|default:'Страница не найдена'|escape}</p>
        <a class="btn" href="/">Вернуться на главную</a>
    </div>
{/block}
