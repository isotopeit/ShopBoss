
<h2 class="text-center">{{ settings()->company_name }}</h2>
<h4 class="text-center">Product Wise Purchase Report</h4>
<p class="text-center">For Period <span class="fw-bold">{{ $from }}</span> To <span class="fw-bold">{{ $to }}</span></p>

<table class="table table-bordered table-striped table-sm mt-2">
    <thead>
        <tr>
            <th>Sl</th>
            <th>Product Name</th>
            <th>Date</th>
            <th>Reference</th>
            <th>Supplier</th>
            <th>Unit Price</th>
            <th>Total Qty</th>
            <th>Total Amount</th>
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
                <td>{{ $item->supplier_name }}</td>
                <td>{{ $item->unit_price }}</td>
                <td>{{ $item->purchase_qty }}</td>
                <td>{{ $item->sub_total }}</td>
            </tr>
            @endforeach
            <tr>
                <th colspan="6" class="text-end">Total :</th>
                <th>{{ $items->sum('purchase_qty') }}</th>
                <th>{{ $items->sum('sub_total') }}</th>
            </tr>
        @endforeach
    </tbody>
</table>