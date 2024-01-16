@extends('isotope::master')

@section('title', 'Sales Payment')

@push('buttons')
    <a href="{{ route('sales.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
        {{ __('List') }}
    </a>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-striped h-100">
                            <thead class="bg-isotope">
                                <tr>
                                    <th>Sl</th>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Amount</th>
                                    <th>Payment Method</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sale->salePayments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->date }}</td>
                                        <td>{{ $payment->reference }}</td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td class="d-flex justify-content-center">
                                            <a title="Edit"
                                                class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                                href="{{ route('sale-payments.edit',$payment->uuid) }}">
                                                <i class="fas fa-edit ms-1"></i>
                                            </a>
                                            <form action="{{ route('sale-payments.destroy', $payment->uuid) }}"
                                                method="post">
                                                @method('delete') @csrf
                                                <button title="Delete" type="submit"
                                                    onclick="return confirm('Are you want to delete this?')"
                                                    class="btn btn-outline btn-outline-dashed btn-outline-danger p-0 me-1">
                                                    <i class="fa-solid fa-trash ms-1"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
