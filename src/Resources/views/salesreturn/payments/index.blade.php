@extends('isotope::master')

@section('title', 'Sale Payments')

@push('buttons')
    <a class="btn btn-sm btn-isotope fw-bold" href="{{ route('sale-returns.index') }}">{{ __('List') }}</a>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h2>Sale Return #{{ $sale_return->reference }}</h2>
                        <table class="table table-bordered table-striped table-sm">
                            <thead class="bg-isotope">
                                <tr>
                                    <td>{{ __('#SL') }}</td>
                                    <td>{{ __('Date') }}</td>
                                    <td>{{ __('Reference') }}</td>
                                    <td>{{ __('Amount') }}</td>
                                    <td>{{ __('Payment Method') }}</td>
                                    <td class="text-center">{{ __('Actions') }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sale_return->saleReturnPayments as $payment)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $payment->date }}</td>
                                        <td>{{ $payment->reference }}</td>
                                        <td>{{ $payment->amount }}</td>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td class="d-flex justify-content-center">
                                            <a title="Edit"
                                                class="btn btn-outline btn-outline-dashed btn-outline-info p-0 me-1"
                                                href="{{ route('sale-return-payments.edit', [$payment->id]) }}">
                                                <i class="fas fa-edit ms-1"></i>
                                            </a>
                                            <form action="{{ route('sale-return-payments.destroy', $payment->id) }}"
                                                method="post">
                                                @method('delete') @csrf
                                                <button title="Delete" type="submit"
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
