{extends file='layout.tpl'}

{block name="content"}
    <nav class="breadcrumbs">
        <a href="/">Главная</a> <span>/</span> <span>{$category->name|escape}</span>
    </nav>

    <h1 class="page-title">{$category->name|escape}</h1>
    {if $category->description}
        <p class="lead">{$category->description|escape}</p>
    {/if}

    <div class="toolbar">
        <span class="toolbar__count">Статей: {$total}</span>
        <div class="toolbar__sort">
            <span>Сортировка:</span>
            <a class="sort-link{if $sort == 'date'} is-active{/if}"
               href="/category/{$category->id}?sort=date">по дате</a>
            <a class="sort-link{if $sort == 'views'} is-active{/if}"
               href="/category/{$category->id}?sort=views">по просмотрам</a>
        </div>
    </div>

    {if $articles}
        <div class="cards">
            {foreach $articles as $item}
                {include file='_card.tpl' article=$item}
            {/foreach}
        </div>

        {if $total_pages > 1}
            <nav class="pagination">
                {foreach $pages as $p}
                    {if $p == $page}
                        <span class="pagination__item is-active">{$p}</span>
                    {else}
                        <a class="pagination__item"
                           href="/category/{$category->id}?sort={$sort}&page={$p}">{$p}</a>
                    {/if}
                {/foreach}
            </nav>
        {/if}
    {else}
        <p class="empty">В этой категории пока нет статей.</p>
    {/if}
{/block}
