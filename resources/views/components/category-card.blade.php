<article class="category-card">
    <span class="category-icon" aria-hidden="true">
        <svg viewBox="0 0 24 24" fill="none"><path d="M6 3.75h8l4 4v12.5H6V3.75Z" stroke="currentColor" stroke-width="1.5"/><path d="M14 3.75v4h4M9 12h6m-6 3.5h6" stroke="currentColor" stroke-width="1.5"/></svg>
    </span>
    <h3><a href="{{ $category->publicCategoryUrl() }}">{{ $category->name }}</a></h3>
    <p>{{ $category->short_description ?: 'Explore the services and available information in this category.' }}</p>
    <a class="text-link" href="{{ $category->publicCategoryUrl() }}">View category <span aria-hidden="true">→</span></a>
</article>
