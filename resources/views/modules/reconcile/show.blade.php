@php
    switch (request()->query('status')) {
        case 'match':
            $status = 'MATCH';
            break;
        case 'dispute':
            $status = 'DISPUTE';
            break;
        case 'onHold':
            $status = 'ON HOLD';
            break;
        default:
            $status = 'DISPUTE';
            break;
    }

    $token = request()->query('token');
    $status = request()->query('status');

    $downloadUrl = '/reconcile/download';
    if ($token) {
        $downloadUrl = $downloadUrl . '?token=' . $token;
    }
    if ($status) {
        if ($token) {
            $downloadUrl = $downloadUrl . '&status=' . $status;
        } else {
            $downloadUrl = $downloadUrl . '?status=' . $status;
        }
    }
@endphp
<x-app-layout>
    <?php
    $can = auth()
        ->user()
        ->hasAnyPermission(['view-disburslist', 'approve-disburslist', 'cancel-disburslist','download-disburslist']);
    $canview = auth()
        ->user()
        ->hasAnyPermission(['view-disburslist']);
    $canapprove = auth()
        ->user()
        ->hasAnyPermission(['approve-disburslist']);
    $cancancel = auth()
        ->user()
        ->hasAnyPermission(['cancel-disburslist']);
    $candownload = auth()
        ->user()
        ->hasAnyPermission(['download-disburslist']);
    
    echo "<script>var authUserCan = '$can';</script>";
    echo "<script>var authUserCanView = '$canview';</script>";
    echo "<script>var authUserCanApprove = '$canapprove';</script>";
    echo "<script>var authUserCanCancel = '$cancancel';</script>";
    echo "<script>var authUserCanDownload = '$candownload';</script>";
    ?>
    <div class="container">
        <div class="card card-flush px-10 py-6 rounded-sm">

            <div class="d-flex flex-wrap justify-content-between">
                <!--begin::Stats-->
                <div class="row w-100">
                    <div class="col-6">
                        <div class="border border-gray-300 border-dashed rounded w-100 py-3 px-4 mb-3">
                            {{-- <a href={{ url('/reconcile/result?status=match&token=' . $token) }} --}}
                            <p class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                                <!--begin::Hidden-->
                            <div class="d-flex flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Match</span>
                                    <span class="text-dark fw-bold" id="resmatch"></span>
                                </div>
                                <div class="fw-bolder fs-5 text-primary" id="ressumMatch"></div>
                            </div>
                            <!--end::Hidden-->
                            </p>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="border border-gray-300 border-dashed rounded w-100 py-3 px-4 mb-3">
                            {{-- <a href={{ url('/reconcile/result?status=dispute&token=' . $token) }} --}}
                            <p class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                                <!--begin::Hidden-->
                            <div class="d-flex flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Variance</span>
                                    <span class="text-dark fw-bold" id="resdispute"></span>
                                </div>
                                <span class="fw-bolder fs-5 text-primary" id="ressumDispute"></span>
                            </div>
                            <!--end::Hidden-->
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!--begin::Wrapper-->
            <div class="d-flex flex-stack mb-5">
                <!--begin::Search-->
                <div class="card-title">
                    @if (request()->query('status') !== null)
                        <div class="fw-bolder fs-3 my-4">Result For {{ $status }} Transaction</div>
                    @endif

                    <!--begin::Search-->
                    <div class="d-flex">
                        <div class="d-flex align-items-center position-relative my-1 rounded-sm">
                            <div class="mb-0 w-250px me-2">
                                <input class="form-control form-control-solid rounded-sm" placeholder="Pick date rage"
                                    id="kt_daterangepicker_1" name="bo_date" />
                            </div>
                        </div>
                        <div class="d-flex align-items-center position-relative my-1">
                            <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                            <span class="svg-icon svg-icon-1 position-absolute ms-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                        rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                    <path
                                        d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                        fill="black" />
                                </svg>
                            </span>
                            <!--end::Svg Icon-->
                            <input type="text" data-kt-docs-table-filter="search" id="searchTable"
                                class="form-control form-control-solid w-250px ps-14 rounded-sm ms-2"
                                placeholder="Search Merchant" />
                        </div>
                        <div class="d-flex align-items-center position-relative my-1 mx-4">
                            <select name="channel" data-placeholder="Select a Channel..."
                                class="w-250px form-select form-select-solid fw-bolder rounded-sm" id="channelId">
                                <option value="">Select a Channel...</option>
                                @foreach ($banks as $item)
                                    <option value="{{ $item->bank_id }}">{{ $item->channel }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <!--end::Search-->
                </div>
                <!--end::Search-->

                <!--begin::Toolbar-->
                <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                    <!--begin::Filter-->
                    <button id="resrefreshButton" class="btn btn-sm btn-light-primary w-100 me-3 rounded-sm">Refresh
                        Table</button>
                    @if (auth()->user()->hasAnyPermission(['download-disburslist']))
                        {{-- <a href="{{ url($downloadUrl) }} " class="btn btn-light-warning me-3 rounded-sm">Download</a> --}}
                        <a href="#" class="btn btn-sm btn-light-warning me-3 rounded-sm" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_download">Download</a>
                    @endif
                    @if (
                        $checkapprove > 0 &&
                            auth()->user()->hasAnyPermission(['approve-disburslist']))
                        <button id="approveAll" class="btn btn-sm btn-success me-3 rounded-sm">
                            Approve All
                        </button>
                    @endif
                    <button class="btn btn-primary rounded-sm me-3" id="bulking" style="display: none">
                        Bulk Approve
                    </button>
                    <button class="btn btn-danger rounded-sm" id="canceling" style="display: none">
                        Bulk Cancel
                    </button>

                    <!--end::Filter-->
                </div>
                <!--end::Toolbar-->

            </div>
            <!--end::Wrapper-->

            <!--begin::Datatable-->

            <table id="kt_datatable_example_99" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-dark fw-bolder fs-7 text-uppercase gs-0">
                        {{-- <th>Settlement Date</th> --}}
                        {{-- <th>Batch</th> --}}
                        {{-- <th>No</th> --}}
                        <th><input type="checkbox" class="form-check-input" id="checkAll"></th>
                        <th>MID / MRC</th>
                        <th>Bank Code</th>
                        <th>Merchant Name</th>
                        <th>Account Number</th>
                        <th>Identify</th>
                        <th>Bank Type</th>
                        <th>Account Name</th>
                        <th>Transfer Amount</th>
                        <th>Sales Amount</th>
                        <th>Bank Transfer</th>
                        <th>Bank Movement</th>
                        <th>Variance</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                </tbody>
            </table>
            <!--end::Datatable-->
        </div>
    </div>
    @include('/modules/reconcile/mrc-modal')
    @include('/modules/reconcile/download-modal')

    @section('scripts')
        <script src="{{ asset('cztemp/assets/custom/js/reconcile.js') }}"></script>
    @endsection
</x-app-layout>
