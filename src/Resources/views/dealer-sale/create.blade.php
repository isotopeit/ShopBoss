@extends('pos::layouts.app')

@section('title', 'Create Dealer Sale')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('dealer-sales.index') }}">{{ __('Dealer Sale') }}</a></li>
        <li class="breadcrumb-item active">{{ __('Add') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product />
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('pos::utils.alerts')
                        <form id="sale-form" action="{{ route('dealer-sales.store') }}" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="reference">{{ __('Reference') }} <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly
                                            value="SL">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="company_id">{{ __('Company') }} <span
                                                    class="text-danger">*</span></label>

                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <p class="btn btn-primary" id="company_create">
                                                        <i class="bi bi-person-plus"></i>
                                                    </p>
                                                </div>
                                                <select class="form-control" name="company_id" id="company_id"
                                                    required></select>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="date">{{ __('Date') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date" required
                                                value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <livewire:product-cart :cartInstance="'dealer-sales'" />

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="Pending">Pending</option>
                                            <option value="Shipped">Shipped</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="payment_method">{{ __('Payment Method') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-control" name="payment_method" id="payment_method" required>
                                                <option value="Cash">Cash</option>
                                                <option value="Credit Card">Credit Card</option>
                                                <option value="Bank Transfer">Bank Transfer</option>
                                                <option value="Cheque">Cheque</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="paid_amount">{{ __('Amount Received') }} <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="paid_amount" type="text" class="form-control" name="paid_amount"
                                                required>
                                            <div class="input-group-append">
                                                <button id="getTotalAmount" class="btn btn-primary" type="button">
                                                    <i class="bi bi-check-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">{{ __('Note (If Needed)') }}</label>
                                <textarea name="note" id="note" rows="5" class="form-control"></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create Dealer Sale') }} <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customer_create_modal" tabindex="-1" role="dialog"
        aria-labelledby="customer_create_modal_label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customer_create_modal_label">Create Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="customer_name">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customer_name" placeholder="Enter Customer Name" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_name">Phone <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="customer_phone" placeholder="Enter Customer Phone" max="11" min="11" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="customer_save">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(document).on('click','#customer_create',customer_create_modal_open);
            $(document).on('click','#customer_save',customer_save);

            compnaySelect2();
        });

        function customer_create_modal_open()
        {
            reset();
            $("#customer_create_modal").modal('show');
        }

        function customer_save()
        {
            if($("#customer_name").val().length < 1 || $("#customer_phone").val().length < 1)
                alert('Name and Phone Required');

            let data = {
                name : $("#customer_name").val(),
                phone: $("#customer_phone").val()
            };

            $.post('/api/customer-store',data,function(res){
                $("#customer_create_modal").modal('hide');
                customerSelect2('#customer_id',res.id);
            });

        }

        function reset()
        {
            $("#customer_name").val('');
            $("#customer_phone").val('');
        }
    </script>
@endpush
