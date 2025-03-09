@extends('layouts/layoutMaster')

@section('title', 'Work Order Management')

<!-- Vendor Styles -->
@section('vendor-style')
    @vite(['resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss', 'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss', 'resources/assets/vendor/libs/flatpickr/flatpickr.scss', 'resources/assets/vendor/libs/select2/select2.scss', 'resources/assets/vendor/libs/@form-validation/form-validation.scss', 'resources/assets/vendor/libs/animate-css/animate.scss', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss', 'resources/assets/vendor/libs/toastr/toastr.scss', 'resources/assets/vendor/libs/animate-css/animate.scss'])
@endsection

<!-- Vendor Scripts -->
@section('vendor-script')
    @vite(['resources/assets/vendor/libs/moment/moment.js', 'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js', 'resources/assets/vendor/libs/flatpickr/flatpickr.js', 'resources/assets/vendor/libs/select2/select2.js', 'resources/assets/vendor/libs/@form-validation/popular.js', 'resources/assets/vendor/libs/@form-validation/bootstrap5.js', 'resources/assets/vendor/libs/@form-validation/auto-focus.js', 'resources/assets/vendor/libs/cleavejs/cleave.js', 'resources/assets/vendor/libs/cleavejs/cleave-phone.js', 'resources/assets/vendor/libs/sweetalert2/sweetalert2.js', 'resources/assets/vendor/libs/toastr/toastr.js'])
@endsection

<!-- Page Scripts -->
@section('page-script')
    <script>
        const canCreateWorkOrder = @json(auth()->user()->can('create work order'));
        const canUpdateWorkOrder = @json(auth()->user()->can('update work order'));
        const canUpdateOwnWorkOrder = @json(auth()->user()->can('update own work order'));
        const canDeleteWorkOrder = @json(auth()->user()->can('delete work order'));
    </script>
    @vite(['resources/assets/js/ui-toasts.js', 'resources/assets/js/form-layouts.js', 'resources/js/work-order.js'])
@endsection

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Work Order</h5>
        </div>
        <div class="card-body">
            <form class="dt_adv_search" id="filter-form" method="POST">
                <div class="row">
                    <div class="col-12">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Status:</label>
                                <select type="text" class="form-select dt-input dt-full-name" data-column=1
                                    placeholder="Status" data-column-index="0" id="filter-status">
                                    <option value="">Select Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="canceled">Canceled</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label class="form-label">Date:</label>
                                <div class="mb-0">
                                    <input type="text" class="form-control dt-date dob-picker flatpickr-input"
                                        data-column="2" data-column-index="1" name="dt_date" placeholder="YYYY-MM-DD"
                                        readonly="readonly" id="filter-date" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-sm-12">
                        <div class="d-flex align-items-center">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <button type="reset" class="btn btn-label-secondary ms-2" id="reset-filter">Reset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-datatable table-responsive">
            <table class="datatables-work-orders table">
                <thead class="border-top">
                    <tr>
                        <th></th>
                        <th>No</th>
                        <th>Reference</th>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Operator</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
