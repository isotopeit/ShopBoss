<div class="btn-group btn-group-sm dropstart">
    <button type="button" class="btn btn-sm text-dark dropdown p-0 m-0" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-list text-dark"></i>
    </button>
    <div class="dropdown-menu">
        @can('access_purchase_payments')
            <a href="{{ route('purchase-payments.index', 'purchase_id='.$purchase->id) }}" class="dropdown-item">
                <i class="bi bi-cash-coin mr-2 text-warning" style="line-height: 1;"></i> {{ __('Show Payments') }}
            </a>
        @endcan
        @can('access_purchase_payments')
            @if($purchase->due_amount > 0)
                <a href="{{ route('purchase-payments.create', 'purchase_id='.$purchase->id) }}" class="dropdown-item">
                    <i class="bi bi-plus-circle-dotted mr-2 text-success" style="line-height: 1;"></i> {{ __('Add Payment') }}
                </a>
            @endif
        @endcan
        @can('edit_purchases')
            <a href="{{ route('purchases.edit', $purchase->id) }}" class="dropdown-item">
                <i class="bi bi-pencil mr-2 text-primary" style="line-height: 1;"></i> {{ __('Edit') }}
            </a>
        @endcan
        @can('show_purchases')
            <a href="{{ route('purchases.show', $purchase->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i> {{ __('Details') }}
            </a>
        @endcan
        @can('delete_purchases')
            <button id="delete" class="dropdown-item" onclick="
                event.preventDefault();
                if (confirm('Are you sure? It will delete the data permanently!')) {
                document.getElementById('destroy{{ $purchase->id }}').submit()
                }">
                <i class="bi bi-trash mr-2 text-danger" style="line-height: 1;"></i> {{ __('Delete') }}
                <form id="destroy{{ $purchase->id }}" class="d-none" action="{{ route('purchases.destroy', $purchase->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
