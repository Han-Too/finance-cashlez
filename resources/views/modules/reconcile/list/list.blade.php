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

    $priv = App\Helpers\Utils::getPrivilege('reconcile');
@endphp
<x-app-layout>
    <div class="container">
        <div class="card card-flush px-10 py-6 rounded-sm">
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack mb-5">
                <!--begin::Search-->
                <div class="card-title">
                    <!--begin::Search-->
                    <div class="d-flex align-items-center position-relative my-1">
                        <!--begin::Svg Icon | path: icons/duotune/general/gen021.svg-->
                        <span class="svg-icon svg-icon-1 position-absolute ms-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                    rx="1" transform="rotate(45 17.0365 15.1223)" fill="black" />
                                <path
                                    d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                    fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                        <input type="text" data-kt-docs-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14 rounded-sm"
                            placeholder="Search Bank" />
                    </div>
                    <!--end::Search-->
                </div>
                <!--end::Search-->

                <!--begin::Toolbar-->
                @if ($priv->create)
                    <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                        <a href="#" class="btn btn-light-primary me-3 rounded-sm" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_reconcile">Add New Record</a>
                        <!--end::Filter-->
                    </div>
                @endif
                <!--end::Toolbar-->

            </div>
            <!--end::Wrapper-->

            <!--begin::Datatable-->
            <table id="kt_datatable_example_2" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-dark fw-bolder fs-7 text-uppercase gs-0">
                        <th>No</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Settlement File</th>
                        <th class="">BO Date</th>
                        <th class="">Reconcile Date</th>
                        <th class="text-center">Reconcile</th>
                        <th class="text-center">Status</th>
                        <th class="text-end min-w-100px">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                </tbody>
            </table>
            <!--end::Datatable-->
        </div>
    </div>

    @include('modules.reconcile.list.modal_recon')


    @section('scripts')
        <script>
            var privCreate = {!! $priv->create !!};
        </script>
        <script src="{{ asset('cztemp/assets/custom/js/reconcile_list.js') }}"></script>
    @endsection
</x-app-layout>
