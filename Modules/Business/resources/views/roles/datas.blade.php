@foreach ($users as $user)
    <tr>
        <td class="w-60 checkbox d-print-none">
            <input type="checkbox" name="ids[]" class="delete-checkbox-item multi-delete" value="{{ $user->id }}">
        </td>
        <td>{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>

        <td>{{ $user->name }}</td>
        <td>
            @php
                $count = 0;
                foreach ($user->visibility ?? [] as $module) {
                    // Only count when module has nested permissions (array)
                    if (is_array($module)) {
                        foreach ($module as $value) {
                            if ($value == '1' || $value === true) {
                                $count++;
                            }
                        }
                    }
                }
            @endphp
            {{ $count }}
        </td>
        <td class="d-print-none">
            <div class="dropdown table-action">
                <button type="button" data-bs-toggle="dropdown">
                    <i class="far fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{{ route('business.roles.edit', $user->id) }}">
                            <i class="fal fa-edit"></i>
                            {{ __('Edit') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('business.roles.destroy', $user->id) }}" class="confirm-action"
                            data-method="DELETE">
                            <i class="fal fa-trash-alt"></i>
                            {{ __('Delete') }}
                        </a>
                    </li>
                </ul>
            </div>
        </td>
    </tr>
@endforeach
