@extends('pos::layouts.app')

@section('title', __('shopboss::shopboss.editUser'))

@section('third_party_stylesheets')
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet"/>
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css"
          rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('shopboss::shopboss.home') }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">{{ __('shopboss::shopboss.users') }}</a></li>
        <li class="breadcrumb-item active">{{ __('shopboss::shopboss.edit') }}</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('patch')
            <div class="row">
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    @include('pos::utils.alerts')

                                    <div class="form-group">
                                        <label for="name">{{ __('shopboss::shopboss.name') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="text" name="name" required value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="email">{{ __('shopboss::shopboss.email') }} <span class="text-danger">*</span></label>
                                        <input class="form-control" type="email" name="email" required value="{{ $user->email }}">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="role">{{ __('shopboss::shopboss.role') }} <span class="text-danger">*</span></label>
                                <select class="form-control" name="role" id="role" required>
                                    @foreach(\Isotope\Authorization\Models\Role::where('name', '!=', 'Super Admin')->get() as $role)
                                        <option {{ $user->hasRole($role->name) ? 'selected' : '' }} value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="is_active">{{ __('shopboss::shopboss.status') }} <span class="text-danger">*</span></label>
                                <select class="form-control" name="is_active" id="is_active" required>
                                    <option value="1" {{ $user->is_active == 1 ? 'selected' : ''}}>{{ __('shopboss::shopboss.active') }}</option>
                                    <option value="2" {{ $user->is_active == 2 ? 'selected' : ''}}>{{ __('shopboss::shopboss.deactive') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="image">{{ __('shopboss::shopboss.profileImage') }} <span class="text-danger">*</span></label>
                                <img style="width: 100px;height: 100px;" class="d-block mx-auto img-thumbnail img-fluid rounded-circle mb-2" src="{{ $user->getFirstMediaUrl('avatars') }}" alt="Profile Image">
                                <input id="image" type="file" name="image" data-max-file-size="500KB">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 float-right">
                    <div class="form-group">
                        <button class="btn btn-primary">{{ __('shopboss::shopboss.updateUser') }} <i class="bi bi-check"></i></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('third_party_scripts')
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script
        src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script
        src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
@endsection

@push('page_scripts')
    <script>
        FilePond.registerPlugin(
            FilePondPluginImagePreview,
            FilePondPluginFileValidateSize,
            FilePondPluginFileValidateType
        );
        const fileElement = document.querySelector('input[id="image"]');
        const pond = FilePond.create(fileElement, {
            acceptedFileTypes: ['image/png', 'image/jpg', 'image/jpeg'],
        });
        FilePond.setOptions({
            server: {
                url: "{{ route('filepond.upload') }}",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            }
        });
    </script>
@endpush


