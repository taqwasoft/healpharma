@foreach ($categories as $category)
    <div class="category-content category-list" data-id="{{ $category->id }}"
        data-route="{{ route('business.purchases.product-filter') }}">
        <h6 class="category-name">{{ $category->categoryName }}</h6>
    </div>
@endforeach
