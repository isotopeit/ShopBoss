<h2 class="text-center">{{ settings()->company_name }}</h2>
<h4 class="text-center">{{ __('shopboss::shopboss.productWiseSaleReturnReport') }}</h4>
<p class="text-center">{{ __('shopboss::shopboss.forPeriod') }} <span class="fw-bold">{{ $from }}</span> {{ __('shopboss::shopboss.to') }} <span class="fw-bold">{{ $to }}</span></p>

<table class="table table-bordered table-striped table-sm mt-2">
    <thead>
        <tr>
            <th>{{ __('shopboss::shopboss.sl') }}</th>
            <th>{{ __('shopboss::shopboss.productName') }}</th>
            <th>{{ __('shopboss::shopboss.date') }}</th>
            <th>{{ __('shopboss::shopboss.reference') }}</th>
            <th>{{ __('shopboss::shopboss.customer') }}</th>
            <th>{{ __('shopboss::shopboss.unitPrice') }}</th>
            <th>{{ __('shopboss::shopboss.totalQty') }}</th>
            <th>{{ __('shopboss::shopboss.totalAmount') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $items)
            <tr>
                <th colspan="8">{{ $items[0]->product_name }}</th>
            </tr>
            @foreach ($items as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->reference }}</td>
                <td>{{ $item->customer_name }}</td>
                <td>{{ $item->unit_price }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->sub_total }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="6" class="text-end">{{ __('shopboss::shopboss.total') }} :</th>
                <th>{{ $items->sum('quantity') }}</th>
                <th>{{ $items->sum('sub_total') }}</th>
            </tr>
        @endforeach
    </tbody>
</table>
</table>
