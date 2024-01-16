<div class="btn-group dropleft">
    <button type="button" class="btn btn-sm text-dark dropdown p-0 m-0" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-list text-dark"></i>
    </button>
    <div class="dropdown-menu">
        @can('access_sale_payments')
            <a href="{{ route('sale-return-payments.index', $sale_return->id) }}" class="dropdown-item">
                <i class="bi bi-cash-coin mr-2 text-warning" style="line-height: 1;"></i> {{ __('Show Payments') }}
            </a>
        @endcan
        @can('access_sale_payments')
            @if($sale_return->due_amount > 0)
                <a href="{{ route('sale-return-payments.create', $sale_return->id) }}" class="dropdown-item">
                    <i class="bi bi-plus-circle-dotted mr-2 text-success" style="line-height: 1;"></i> {{ __('Add Payment') }}
                </a>
            @endif
        @endcan
        @can('edit_sales')
            <a href="{{ route('sale-returns.edit', $sale_return->id) }}" class="dropdown-item">
                <i class="bi bi-pencil mr-2 text-primary" style="line-height: 1;"></i> {{ __('Edit') }}
            </a>
        @endcan
        @can('show_sales')
            <a href="{{ route('sale-returns.show', $sale_return->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i> {{ __('Details') }}
            </a>
        @endcan
        @can('delete_sales')
            <button id="delete" class="dropdown-item" onclick="
                event.preventDefault();
                if (confirm('Are you sure? It will delete the sale_return permanently!')) {
                document.getElementById('destroy{{ $sale_return->id }}').submit()
                }">
                <i class="bi bi-trash mr-2 text-danger" style="line-height: 1;"></i> {{ __('Delete') }}
                <form id="destroy{{ $sale_return->id }}" class="d-none" action="{{ route('sale-returns.destroy', $sale_return->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
