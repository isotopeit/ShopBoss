<h2 class="text-center">{{ settings()->company_name }}</h2>
<h4 class="text-center">{{ __('shopboss::shopboss.salesReturnReport') }}</h4>
<p class="text-center">{{ __('shopboss::shopboss.forPeriod') }} <span class="fw-bold">{{ $from }}</span> {{ __('shopboss::shopboss.to') }} <span class="fw-bold">{{ $to }}</span></p>

<table class="table table-bordered table-striped table-sm mt-2">
    <thead>
        <tr>
            <th>{{ __('shopboss::shopboss.sl') }}</th>
            <th>{{ __('shopboss::shopboss.date') }}</th>
            <th>{{ __('shopboss::shopboss.reference') }}</th>
            <th>{{ __('shopboss::shopboss.customer') }}</th>
            <th>{{ __('shopboss::shopboss.total') }}</th>
            <th>{{ __('shopboss::shopboss.paid') }}</th>
            <th>{{ __('shopboss::shopboss.due') }}</th>
            <th>{{ __('shopboss::shopboss.paymentStatus') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->reference }}</td>
                <td>{{ $item->customer_name }}</td>
                <td>{{ $item->total_amount }}</td>
                <td>{{ $item->paid_amount }}</td>
                <td>{{ $item->due_amount }}</td>
                <td>{{ $item->payment_status }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-end">{{ __('shopboss::shopboss.total') }} :</th>
            <th>{{ $data->sum('total_amount') }}</th>
            <th>{{ $data->sum('paid_amount') }}</th>
            <th>{{ $data->sum('due_amount') }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>