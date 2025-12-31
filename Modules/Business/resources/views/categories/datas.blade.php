@foreach($categories as $category)
    <tr>
        <td class="w-60 checkbox table-single-content">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item  multi-delete" value="{{ $category->id }}">
        </td>
        <td class="table-single-content">{{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}</td>
        <td class="text-start table-single-content">{{ $category->categoryName }}</td>
        <td class="table-single-content">{{ Str::limit($category->description, 20, '...') }}</td>
        <td class="text-center table-single-content">
            <label class="switch">
                <input type="checkbox" {{ $category->status == 1 ? 'checked' : '' }} class="status" data-url="{{ route('business.categories.status', $category->id) }}">
                <span class="slider round"></span>
            </label>
        </td>
        <td class="print-d-none table-single-content">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#category-edit-modal" class="category-edit-btn" data-bs-toggle="modal"
                           data-url="{{ route('business.categories.update', $category->id) }}"
                           data-category-name="{{ $category->categoryName }}"
                           data-category-description="{{ $category->description }}"
                           >
                            <i class="fal fa-edit"></i>{{__('Edit')}}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.categories.destroy', $category->id) }}" class="confirm-action" data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
