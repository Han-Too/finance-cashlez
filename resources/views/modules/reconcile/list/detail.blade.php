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
        ->hasAnyPermission(['view-reconlist', 'create-reconlist', 'update-reconlist', 'delete-reconlist', 'download-reconlist', 'checker-reconlist', 'auto-reconlist', 'manual-reconlist']);
    $canview = auth()
        ->user()
        ->hasAnyPermission(['view-reconlist']);
    $cancreate = auth()
        ->user()
        ->hasAnyPermission(['create-reconlist']);
    $candelete = auth()
        ->user()
        ->hasAnyPermission(['delete-reconlist']);
    $candownload = auth()
        ->user()
        ->hasAnyPermission(['download-reconlist']);
    $cancheck = auth()
        ->user()
        ->hasAnyPermission(['checker-reconlist']);
    $canauto = auth()
        ->user()
        ->hasAnyPermission(['auto-reconlist']);
    $canmanual = auth()
        ->user()
        ->hasAnyPermission(['manual-reconlist']);
    
    echo "<script>var authUserCan = '$can';</script>";
    echo "<script>var authUserCanView = '$canview';</script>";
    echo "<script>var authUserCanCreate = '$cancreate';</script>";
    echo "<script>var authUserCanCheck = '$cancheck';</script>";
    echo "<script>var authUserCanDownload = '$candownload';</script>";
    echo "<script>var authUserCanDelete = '$candelete';</script>";
    echo "<script>var authUserCanAuto = '$canauto';</script>";
    echo "<script>var authUserCanManual = '$canmanual';</script>";
    ?>
    @csrf
    <div id="kt_content_container" class="container-xxl">
        <nav>
            <div class="row nav nav-tabs" id="nav-tab" role="tablist">
                <button class="col nav-link py-3 active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                    type="button" role="tab" aria-controls="nav-home" aria-selected="true">
                    <div class="fw-bold fs-6 text-active-primary">
                        System
                    </div>
                </button>
                @if (auth()->user()->hasAnyPermission(['manual-reconlist']))
                    <button class="col nav-link py-3" id="nav-unmatch-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-unmatch" type="button" role="tab" aria-controls="nav-unmatch"
                        aria-selected="true">
                        <div class="fw-bold fs-6 text-active-primary">
                            Manual
                        </div>
                    </button>
                @endif
                {{-- @if ($draft == 0) --}}
                <button class="col nav-link py-3" id="nav-report-tab" data-bs-toggle="tab" data-bs-target="#nav-report"
                    type="button" role="tab" aria-controls="nav-report" aria-selected="true">
                    <div class="fw-bold fs-6 text-active-primary">
                        Result
                    </div>
                </button>
                {{-- @endif --}}
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

            @include('modules.reconcile.list.tabs.tabdetail')
            
            @if (auth()->user()->hasAnyPermission(['manual-reconlist']))
                @include('modules.reconcile.list.tabs.tabunmatch')
            @endif
            {{-- @if ($draft == 0) --}}
            @include('modules.reconcile.list.tabs.tabreport')
            {{-- @endif --}}
        </div>
    </div>

    @include('modules.reconcile.detail-modal')

    @include('/modules/reconcile/mrc-modal')
    @include('/modules/reconcile/download-modal')

    @section('scripts')
        <script src="{{ asset('cztemp/assets/custom/js/reconcilenew.js') }}"></script>
        <script src="{{ asset('cztemp/assets/custom/js/reconcile_manualdetail.js') }}"></script>
        <script src="{{ asset('cztemp/assets/custom/js/reconcile_draftdetail.js') }}"></script>
    @endsection
</x-app-layout>
