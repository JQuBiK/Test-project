{extends file='layout.tpl'}

{block name="content"}
    <nav class="breadcrumbs">
        <a href="/">Главная</a> <span>/</span> <span>{$article->title|escape}</span>
    </nav>

    <article class="post">
        <h1 class="post__title">{$article->title|escape}</h1>

        <div class="post__meta">
            <span class="post__date">{$article->publishedAt|rudate}</span>
            <span class="post__views">{$article->views} просмотров</span>
            {foreach $article->categories as $category}
                <a class="tag" href="/category/{$category->id}">{$category->name|escape}</a>
            {/foreach}
        </div>

        {if $article->image}
            <img class="post__image"
                 src="/assets/uploads/{$article->image|escape}"
                 alt="{$article->title|escape}">
        {/if}

        {if $article->description}
            <p class="post__lead">{$article->description|escape}</p>
        {/if}

        <div class="post__body">{$article->body|escape|nl2br}</div>
    </article>

    {if $similar}
        <section class="similar">
            <h2 class="section-title">Похожие статьи</h2>
            <div class="cards">
                {foreach $similar as $item}
                    {include file='_card.tpl' article=$item}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
