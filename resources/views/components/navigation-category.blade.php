<li class="nav-category-item has-submenu">
    <div class="nav-category-heading">
        <a href="{{ $category->publicCategoryUrl() }}">{{ $category->name }}</a>
        <button class="nav-submenu-toggle" type="button" aria-expanded="false" aria-controls="category-navigation-{{ $category->id }}" data-submenu-toggle>
            <span class="nav-chevron" aria-hidden="true"></span><span class="visually-hidden">Show {{ $category->name }} services</span>
        </button>
    </div>
    <ul class="nav-submenu" id="category-navigation-{{ $category->id }}">
        @foreach ($category->navigation_children as $childCategory)
            @include('components.navigation-category', ['category' => $childCategory])
        @endforeach
        @foreach ($category->navigation_services as $service)
            <li class="nav-service-link"><a href="{{ $service->publicUrl() }}">{{ $service->title }}</a></li>
        @endforeach
        @if ($category->navigation_children->isEmpty() && $category->navigation_services->isEmpty())
            <li class="nav-empty">No published services yet.</li>
        @endif
        <li class="nav-view-all"><a href="{{ $category->publicCategoryUrl() }}">View all {{ $category->name }} services</a></li>
    </ul>
</li>
