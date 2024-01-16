@extends('isotope::master')

@section('title', 'Create Product')

@push('buttons')
<a href="{{ route('products.index') }}" type="button" class="btn btn-sm btn-isotope fw-bold">
    {{ __('Product List') }}
</a>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <form id="product-form" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-1 row">
                <label class="col-md-2" for="category_id">{{ __('Category') }}</label>
                <div class="col-md-10">
                    <select class="form-select form-select-sm" name="category_id" id="category_id" required>
                        <option value="" selected disabled>Select Category</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-1 row">
                <label class="col-md-2" for="product_name">{{ __('Product Name') }}</label>
                <div class="col-md-10">
                    <input type="text" class="form-control form-control-sm" name="product_name" required value="{{ old('product_name') }}"
                        placeholder="{{ __('Enter Product Name') }}">
                </div>
            </div>
            <div class="mb-1 row">
                <label class="col-md-2" for="product_code">{{ __('Code') }}</label>
                <div class="col-md-10">
                    <input type="text" class="form-control form-control-sm" name="product_code" required value="{{ old('product_code') }}"
                        placeholder="{{ __('Enter Product Code') }}">
                </div>
            </div>
            <div class="mb-1 row">
                <label class="col-md-2" for="product_cost">{{ __('Cost') }}</label>
                <div class="col-md-10">
                    <input id="product_cost" type="number" class="form-control form-control-sm" name="product_cost" required
                        value="{{ old('product_cost') }}" placeholder="Per Product Cost">
                </div>
            </div>
            <div class="mb-1 row">
                <label class="col-md-2" for="product_price">{{ __('Price') }}</label>
                <div class="col-md-10">
                    <input id="product_price" type="number" class="form-control form-control-sm" name="product_price" required
                        value="{{ old('product_price') }}" placeholder="Per Product Price">
                </div>
            </div>
            <div class="mb-1 row">
                <label class="col-md-2" for="product_stock_alert">{{ __('Alert Quantity') }}(Max 100)</label>
                <div class="col-md-10">
                    <input type="number" class="form-control form-control-sm" name="product_stock_alert" required
                        value="{{ old('product_stock_alert') }}" min="0" max="100"
                        placeholder="{{ __('Enter Product Stock Alert') }}">
                </div>
            </div>
            <div class="mb-1 row">
                <label class="col-md-2" for="product_unit">{{ __('UoM') }} <i class="bi bi-question-circle-fill text-info"
                        data-toggle="tooltip" data-placement="top"
                        title="This text will be placed after Product Quantity."></i></label>
                <div class="col-md-10">
                    <input type="text" class="form-control form-control-sm" max="16" name="uom" value="{{ old('product_unit') }}" required
                        placeholder="{{ __('Enter Product Unit') }}">
                </div>
            </div>
            <div class="mb-1">
                <label for="product_note">{{ __('Note') }}</label>
                <textarea name="product_note" id="product_note" rows="2" class="form-control"></textarea>
            </div>
            <div class="mb-1">
                <label class="col-md-2" for="image">{{ __('Product Images') }}
                    <i class="bi bi-question-circle-fill text-info" data-toggle="tooltip" data-placement="top" title="Max Files: 3, Max File Size: 1MB, Image Size: 400x400"></i>
                </label>
                <div class="dropzone d-flex flex-wrap align-items-center justify-content-center" id="document-dropzone">
                    <div class="dz-message" data-dz-message>
                        <i class="bi bi-cloud-arrow-up" style="font-size: 8rem"></i>
                    </div>
                </div>
            </div>
            <div class="mb-1">
                <button class="btn btn-sm btn-isotope float-end mt-3">
                    {{ __('Create Product') }}
                    <i class="bi bi-check"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('js/dropzone.js') }}"></script>
<script>
    var uploadedDocumentMap = {}
        Dropzone.options.documentDropzone = {
            url: '{{ route('dropzone.upload') }}',
            maxFilesize: 1,
            acceptedFiles: '.jpg, .jpeg, .png',
            maxFiles: 3,
            addRemoveLinks: true,
            dictRemoveFile: "<i class='bi bi-x-circle text-danger'></i> remove",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            success: function(file, response) {
                $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">');
                uploadedDocumentMap[file.name] = response.name;
            },
            removedfile: function(file) {
                file.previewElement.remove();
                var name = '';
                if (typeof file.file_name !== 'undefined') {
                    name = file.file_name;
                } else {
                    name = uploadedDocumentMap[file.name];
                }
                $.ajax({
                    type: "POST",
                    url: "{{ route('dropzone.delete') }}",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'file_name': `${name}`
                    },
                });
                $('form').find('input[name="document[]"][value="' + name + '"]').remove();
            }
        }
</script>
@endpush