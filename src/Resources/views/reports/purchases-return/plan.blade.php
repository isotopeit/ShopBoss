
<h2 class="text-center">{{ settings()->company_name }}</h2>
<h4 class="text-center">Purchase Return Report</h4>
<p class="text-center">For Period <span class="fw-bold">{{ $from }}</span> To <span class="fw-bold">{{ $to }}</span></p>

<table class="table table-bordered table-striped table-sm mt-2">
    <thead>
        <tr>
            <th>Sl</th>
            <th>Date</th>
            <th>Reference</th>
            <th>Supplier</th>
            <th>Total</th>
            <th>Paid</th>
            <th>Due</th>
            <th>Payment Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->date }}</td>
                <td>{{ $item->reference }}</td>
                <td>{{ $item->supplier_name }}</td>
                <td>{{ $item->total_amount }}</td>
                <td>{{ $item->paid_amount }}</td>
                <td>{{ $item->due_amount }}</td>
                <td>{{ $item->payment_status }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4" class="text-end">Total :</th>
            <th>{{ $data->sum('total_amount') }}</th>
            <th>{{ $data->sum('paid_amount') }}</th>
            <th>{{ $data->sum('due_amount') }}</th>
            <th></th>
        </tr>
    </tfoot>
</table>