<article class="card">
    <a class="card__link" href="/article/{$article->id}">
        <div class="card__media">
            {if $article->image}
                <img class="card__image"
                     src="/assets/uploads/{$article->image|escape}"
                     alt="{$article->title|escape}"
                     loading="lazy">
            {/if}
        </div>
        <div class="card__body">
            <h3 class="card__title">{$article->title|escape}</h3>
            {if $article->description}
                <p class="card__excerpt">{$article->description|escape}</p>
            {/if}
            <div class="card__meta">
                <span class="card__date">{$article->publishedAt|rudate}</span>
                <span class="card__views">{$article->views} просмотров</span>
            </div>
        </div>
    </a>
</article>
