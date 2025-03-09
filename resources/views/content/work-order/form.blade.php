@extends('layouts/layoutMaster')

@section('title', 'Work Order')

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
        <h5 class="card-header">Work Order</h5>
        <form class="card-body"
            action="{{ isset($workOrder) ? route('work-orders.update', $workOrder->id) : route('work-orders.store') }}"
            method="POST">
            @if (isset($workOrder))
                @method('PUT')
            @endif
            @csrf
            <div class="row g-6">
                <div class="col-md-6">
                    <label class="form-label" for="multicol-product-name">Product</label>
                    <input type="text" id="multicol-product-name" name="product_name" class="form-control"
                        placeholder="Coco crunch" value="{{ isset($workOrder) ? $workOrder->product_name : '' }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="multicol-quantity">Quantity</label>
                    <input type="number" id="multicol-quantity" name="quantity" class="form-control" placeholder="100"
                        value="{{ isset($workOrder) ? $workOrder->quantity : '' }}" />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="multicol-due-date">Due Date</label>
                    <input type="text" id="multicol-due-date" class="form-control dob-picker flatpickr-input"
                        name="due_date" placeholder="YYYY-MM-DD" value="{{ isset($workOrder) ? $workOrder->due_date : '' }}"
                        readonly="readonly">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="collapsible-operator">Operator</label>
                    <select id="collapsible-operator" class="select2 form-select" name="operator">
                        @foreach ($operators as $operator)
                            <option value="{{ $operator->id }}"
                                {{ isset($workOrder) && $workOrder->operator == $operator->id ? 'selected' : '' }}>
                                {{ $operator->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if (isset($workOrder))
                    <div class="col-md-6">
                        <label class="form-label" for="collapsible-status">Status</label>
                        <select id="collapsible-status" class="select2 form-select" name="status">
                            <option value="pending" {{ $workOrder->status == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="in_progress" {{ $workOrder->status == 'in_progress' ? 'selected' : '' }}>
                                In Progress</option>
                            <option value="completed" {{ $workOrder->status == 'completed' ? 'selected' : '' }}>
                                Completed</option>
                            <option value="canceled" {{ $workOrder->status == 'canceled' ? 'selected' : '' }}>
                                Canceled</option>
                        </select>
                    </div>
                @endif
            </div>
            <div class="pt-6">
                <button type="submit" class="btn btn-primary me-4">Submit</button>
                <a href="{{ route('work-orders.index') }}" class="btn btn-label-secondary">Cancel</a>
            </div>
        </form>
    </div>

    @if (isset($workOrder))
        <div class="card mb-6">
            <h5 class="card-header">Progress</h5>
            <div class="card-body">
                <ul class="timeline timeline-outline mb-0">
                    @foreach ($workOrder->progresses as $progress)
                        <li class="timeline-item timeline-item-transparent border-left-dashed">
                            <span class="timeline-point timeline-point-primary"></span>
                            <div class="timeline-event">
                                <div class="timeline-header mb-3">
                                    <h6 class="mb-0">{{ $progress->progress_note }}</h6>
                                    <small class="text-muted">{{ $progress->created_at }}</small>
                                </div>
                                <p class="mb-2">
                                    {{ $progress->quantity }}
                                </p>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="badge bg-lighter rounded d-flex align-items-center">
                                        <span
                                            class="h6 mb-0 text-body">{{ str_replace('_', ' ', ucwords($progress->status)) }}</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

@endsection
