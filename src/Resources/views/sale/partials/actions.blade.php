<div class="btn-group btn-group-sm dropstart">
    <button type="button" class="btn btn-sm text-dark dropdown p-0 m-0" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-list text-dark"></i>
    </button>
    <div class="dropdown-menu">
        <a target="_blank" href="{{ route('sales.pos.pdf', $sale->id) }}" class="dropdown-item">
            <i class="bi bi-file-earmark-pdf mr-2 text-success" style="line-height: 1;"></i> {{ __('shopboss::shopboss.posInvoice') }}
        </a>
        @can('access_sale_payments')
            <a href="{{ route('sale-payments.index','sale_id='. $sale->id) }}" class="dropdown-item">
                <i class="bi bi-cash-coin mr-2 text-warning" style="line-height: 1;"></i> {{ __('shopboss::shopboss.showPayments') }}
            </a>
        @endcan
        @can('access_sale_payments')
            @if($sale->due_amount > 0)
            <a href="{{ route('sale-payments.create','sale_id='. $sale->id) }}" class="dropdown-item">
                <i class="bi bi-plus-circle-dotted mr-2 text-success" style="line-height: 1;"></i> {{ __('shopboss::shopboss.addPayment') }}
            </a>
            @endif
        @endcan
        @can('edit_sales')
            <a href="{{ route('sales.edit', $sale->id) }}" class="dropdown-item">
                <i class="bi bi-pencil mr-2 text-primary" style="line-height: 1;"></i> {{ __('shopboss::shopboss.edit') }}
            </a>
        @endcan
        @can('show_sales')
            <a href="{{ route('sales.show', $sale->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i> {{ __('shopboss::shopboss.details') }}
            </a>
        @endcan
        @can('delete_sales')
            <button id="delete" class="dropdown-item" onclick="
                event.preventDefault();
                if (confirm('{{ __('shopboss::shopboss.areYouSure') }}')) {
                document.getElementById('destroy{{ $sale->id }}').submit()
                }">
                <i class="bi bi-trash mr-2 text-danger" style="line-height: 1;"></i> {{ __('shopboss::shopboss.delete') }}
                <form id="destroy{{ $sale->id }}" class="d-none" action="{{ route('sales.destroy', $sale->id) }}" method="POST">
                    @csrf
                    @method('delete')
                </form>
            </button>
        @endcan
    </div>
</div>
