{extends file='layout.tpl'}

{block name="content"}
    <h1 class="page-title">Последние статьи</h1>

    {if $blocks}
        {foreach $blocks as $block}
            {$category = $block.category}
            <section class="category-block">
                <div class="category-block__head">
                    <h2 class="category-block__title">{$category->name|escape}</h2>
                    <a class="btn" href="/category/{$category->id}">Все статьи</a>
                </div>
                {if $category->description}
                    <p class="category-block__desc">{$category->description|escape}</p>
                {/if}

                <div class="cards">
                    {foreach $block.articles as $item}
                        {include file='_card.tpl' article=$item}
                    {/foreach}
                </div>
            </section>
        {/foreach}
    {else}
        <p class="empty">
            Пока нет ни одной статьи. Запустите сидер командой
            <code>docker compose exec php php database/seed.php</code>.
        </p>
    {/if}
{/block}
