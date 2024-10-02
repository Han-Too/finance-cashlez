<div class="tab-pane fade" id="nav-permission" role="tabpanel" aria-labelledby="nav-permission-tab" tabindex="0">
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
                            <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1"
                                transform="rotate(45 17.0365 15.1223)" fill="black" />
                            <path
                                d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                fill="black" />
                        </svg>
                    </span>
                    <!--end::Svg Icon-->
                    <input type="text" data-kt-docs-tablep-filter="search"
                        class="form-control form-control-solid w-250px ps-14 rounded-sm"
                        placeholder="Search Permission" />
                </div>
                <!--end::Search-->
            </div>
            <!--end::Search-->

            <!--begin::Toolbar-->
            <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                @if (auth()->user()->can(['create-permission']))
                    <a {{-- href="{{ route('permission.add') }}"  --}} class="btn btn-light-primary me-3 rounded-sm" data-bs-toggle="modal"
                        data-bs-target="#kt_modal_addpermis">Add New Permission</a>
                @endif
                <!--end::Filter-->
            </div>
            <!--end::Toolbar-->

        </div>
        <!--end::Wrapper-->

        <!--begin::Datatable-->
        <table id="kt_datatable_permission" class="table align-middle table-row-dashed fs-6 gy-5">
            <thead>
                <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                    <th>No</th>
                    <th>Name</th>
                    <th class="text-end min-w-100px">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 fw-bold">
            </tbody>
        </table>
        <!--end::Datatable-->
    </div>
</div>
<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->


@include('modules.roles.permission.add')
@include('modules.roles.permission.edit')
