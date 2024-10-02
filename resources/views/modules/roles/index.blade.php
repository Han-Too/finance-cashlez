<x-app-layout>
    <div id="kt_content_container" class="container-xxl">
        <nav>
            <div class="row nav nav-tabs" id="nav-tab" role="tablist">
                <button class="col nav-link py-3 active" id="nav-role-tab" data-bs-toggle="tab" data-bs-target="#nav-role"
                    type="button" role="tab" aria-controls="nav-role" aria-selected="true">
                    <div class="fw-bold fs-6 text-active-primary">
                        Role
                    </div>
                </button>
                <button class="col nav-link py-3" id="nav-permission-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-permission" type="button" role="tab" aria-controls="nav-permission"
                    aria-selected="true">
                    <div class="fw-bold fs-6 text-active-primary">
                        Permission
                    </div>
                </button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

            @include('modules.roles.role.index')
            @include('modules.roles.permission.index')

        </div>
    </div>

    @section('scripts')
        <script src="{{ asset('cztemp/assets/custom/js/permission.js') }}"></script>
        <script src="{{ asset('cztemp/assets/custom/js/role.js') }}"></script>
    @endsection
</x-app-layout>
