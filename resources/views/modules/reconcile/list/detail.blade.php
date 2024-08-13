@php
    $priv = App\Helpers\Utils::getPrivilege('reconcile');
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
                <button class="col nav-link py-3" id="nav-unmatch-tab" data-bs-toggle="tab" data-bs-target="#nav-unmatch"
                    type="button" role="tab" aria-controls="nav-unmatch" aria-selected="true">
                    <div class="fw-bold fs-6 text-active-primary">
                        Manual
                    </div>
                </button>
                {{-- @if ($draft == 0) --}}
                    <button class="col nav-link py-3" id="nav-report-tab" data-bs-toggle="tab" data-bs-target="#nav-report"
                        type="button" role="tab" aria-controls="nav-report" aria-selected="true">
                        <div class="fw-bold fs-6 text-active-primary">
                            Disbursement List
                        </div>
                    </button>
                {{-- @endif --}}
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            
            @include('modules.reconcile.list.tabs.tabdetail')
            @include('modules.reconcile.list.tabs.tabunmatch')
            
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
