<x-app-layout>
    <?php
    $canrole = auth()
        ->user()
        ->hasAnyPermission(['view-role', 'delete-role']);
    $candeleterole = auth()
        ->user()
        ->hasAnyPermission(['delete-role']);
    $caneditrole = auth()
        ->user()
        ->hasAnyPermission(['update-role']);
    $canviewrole = auth()
        ->user()
        ->hasAnyPermission(['view-role']);
    
    echo "<script>var authUserCanRole = '$canrole';</script>";
    echo "<script>var authUserCanDeleteRole = '$candeleterole';</script>";
    echo "<script>var authUserCanEditRole = '$caneditrole';</script>";
    echo "<script>var authUserCanViewRole = '$canviewrole';</script>";

    
    $canpermission = auth()
        ->user()
        ->hasAnyPermission(['view-permission', 'delete-permission']);
    $candeletepermission = auth()
        ->user()
        ->hasAnyPermission(['delete-permission']);
    $caneditpermission = auth()
        ->user()
        ->hasAnyPermission(['update-permission']);
    $canviewpermission = auth()
        ->user()
        ->hasAnyPermission(['view-permission']);
    
    echo "<script>var authUserCanPermis = '$canpermission';</script>";
    echo "<script>var authUserCanDeletePermis = '$candeletepermission';</script>";
    echo "<script>var authUserCanEditPermis = '$caneditpermission';</script>";
    echo "<script>var authUserCanViewPermis = '$canviewpermission';</script>";
    ?>
    <div id="kt_content_container" class="container-xxl">
        <nav>
            <div class="row nav nav-tabs" id="nav-tab" role="tablist">
                @if (auth()->user()->can(['view-role']))
                    <button class="col nav-link py-3 active" id="nav-role-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-role" type="button" role="tab" aria-controls="nav-role"
                        aria-selected="true">
                        <div class="fw-bold fs-6 text-active-primary">
                            Role
                        </div>
                    </button>
                @endif
                @if (auth()->user()->can(['view-permission']))
                    <button class="col nav-link py-3" id="nav-permission-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-permission" type="button" role="tab" aria-controls="nav-permission"
                        aria-selected="true">
                        <div class="fw-bold fs-6 text-active-primary">
                            Permission
                        </div>
                    </button>
                @endif
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

            @if (auth()->user()->can(['view-role']))
                @include('modules.roles.role.index')
            @endif
            @if (auth()->user()->can(['view-permission']))
                @include('modules.roles.permission.index')
            @endif

        </div>
    </div>

    @section('scripts')
        <script src="{{ asset('cztemp/assets/custom/js/permission.js') }}"></script>
        <script src="{{ asset('cztemp/assets/custom/js/role.js') }}"></script>
    @endsection
</x-app-layout>
