<div class="btn-group btn-group-sm dropstart">
    <button type="button" class="btn btn-sm text-dark dropdown p-0 m-0" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-list text-dark"></i>
    </button>
    <div class="dropdown-menu">
        @can('access_purchase_return_payments')
            <a href="{{ route('purchase-return-payments.index', 'purchase_return_id='.$purchase_return->id) }}" class="dropdown-item">
                <i class="bi bi-cash-coin mr-2 text-warning" style="line-height: 1;"></i> {{ __('shopboss::shopboss.showPayments') }}
            </a>
        @endcan
        @can('access_purchase_return_payments')
            @if($purchase_return->due_amount > 0)
                <a href="{{ route('purchase-return-payments.create', 'purchase_return_id='.$purchase_return->id) }}" class="dropdown-item">
                    <i class="bi bi-plus-circle-dotted mr-2 text-success" style="line-height: 1;"></i> {{ __('shopboss::shopboss.addPayment') }}
                </a>
            @endif
        @endcan
        @can('edit_purchase_returns')
            <a href="{{ route('purchase-returns.edit', $purchase_return->id) }}" class="dropdown-item">
                <i class="bi bi-pencil mr-2 text-primary" style="line-height: 1;"></i> {{ __('shopboss::shopboss.edit') }}
            </a>
        @endcan
        @can('show_purchase_returns')
            <a href="{{ route('purchase-returns.show', $purchase_return->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i> {{ __('shopboss::shopboss.details') }}
            </a>
        @endcan
        @can('delete_purchase_return')
            <button id="delete" class="dropdown-item" onclick="
                event.preventDefault();
                if (confirm('{{ __('shopboss::shopboss.areYouSure') }}')) {
                document.getElementById('destroy{{ $purchase_return->id }}').submit()
                }">
                <i class="bi bi-trash mr-2 text-danger" style="line-height: 1;"></i> {{ __('shopboss::shopboss.delete') }}
                <form id="destroy{{ $purchase_return->id }}" class="d-none" action="{{ route('purchase-returns.destroy', $purchase_return->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
