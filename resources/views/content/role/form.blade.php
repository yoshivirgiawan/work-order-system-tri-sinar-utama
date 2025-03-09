@extends('layouts/layoutMaster')

@section('title', 'Role')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/toastr/toastr.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    @vite(['resources/assets/js/ui-toasts.js', 'resources/assets/js/form-layouts.js'])
@endsection

@section('content')
    <div class="card mb-6">
        <h5 class="card-header">Role</h5>
        <form class="card-body" action="{{ isset($role) ? route('roles.update', $role->id) : route('roles.store') }}"
            method="POST">
            @if (isset($role))
                @method('PUT')
            @endif
            @csrf
            <h6>1. Role Details</h6>
            <div class="row g-6">
                <div class="col-md-12">
                    <label class="form-label" for="multicol-name">Name</label>
                    <input type="text" id="multicol-name" name="name" class="form-control" placeholder="manager"
                        value="{{ isset($role) ? $role->name : '' }}" />
                </div>
            </div>
            <hr class="my-6 mx-n4" />
            <h6>2. Role Permissions</h6>
            <div class="row row-bordered g-6">
                @foreach ($groupedPermissions as $groupedPermission)
                    <div class="col-12 p-6">
                        <p class="text-light fw-medium d-block text-capitalize">{{ $groupedPermission->group }}</p>
                        @foreach ($groupedPermission->permissions as $permission)
                            <div class="form-check form-check-inline mt-4">
                                <input class="form-check-input" type="checkbox" id="permission-{{ $permission->id }}"
                                    value="{{ $permission->name }}" name="permissions[]"
                                    {{ isset($role) ? ($role->hasPermissionTo($permission->name) ? 'checked' : '') : '' }}>
                                <label class="form-check-label"
                                    for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="pt-6">
                <button type="submit" class="btn btn-primary me-4">Submit</button>
                <a href="{{ route('roles.index') }}" class="btn btn-label-secondary">Cancel</a>
            </div>
        </form>
    </div>

@endsection
