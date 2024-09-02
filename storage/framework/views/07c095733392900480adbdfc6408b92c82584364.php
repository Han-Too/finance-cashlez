<?php
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
        } else{
            $downloadUrl = $downloadUrl . '?status=' . $status;
        }
    }
?>
<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="container">
        <div class="card card-flush px-10 py-6 rounded-sm">

            <div class="d-flex flex-wrap justify-content-between">
                <!--begin::Stats-->
                <div class="d-flex flex-wrap ">
                    <div class="border border-gray-300 border-dashed rounded  w-auto py-3 px-4 me-6 mb-3">
                        
                        <p  class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                            <!--begin::Hidden-->
                            <div class="d-flex flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Not Match</span>
                                    <span class="text-gray-400 fw-bold"><?php echo e($resmatch); ?> Trx</span>
                                </div>
                                <div class="fw-bolder fs-5 text-primary">IDR Rp. <?php echo e(number_format($ressumMatch)); ?></div>
                            </div>
                            <!--end::Hidden-->
                        </p>
                    </div>
                    <div class="border border-gray-300 border-dashed rounded  w-auto py-3 px-4 me-6 mb-3">
                        
                        <p  class="card-body p-0 d-flex justify-content-between flex-column overflow-hidden">
                            <!--begin::Hidden-->
                            <div class="d-flex flex-stack flex-wrap flex-grow-1 px-2 pt-2 pb-3">
                                <div class="me-2">
                                    <span class="fw-bolder text-gray-800 d-block fs-3">Variance</span>
                                    <span class="text-gray-400 fw-bold"><?php echo e($resdispute); ?> Trx</span>
                                </div>
                                <div class="fw-bolder fs-5 text-primary">IDR Rp. <?php echo e(number_format($ressumDispute)); ?></div>
                            </div>
                            <!--end::Hidden-->
                        </p>
                    </div>
                </div>
            </div>
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack mb-5">
                <!--begin::Search-->
                <div class="card-title">
                    <?php if(request()->query('status') !== null): ?>
                        <div class="fw-bolder fs-3 my-4">Result For <?php echo e($status); ?> Transaction</div>
                    <?php endif; ?>
    
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
                            <input type="text" data-kt-docs-table-filter="search" id="searchTable"
                                class="form-control form-control-solid w-250px ps-14 rounded-sm ms-2"
                                placeholder="Search Merchant" />
                        </div>
                        <div class="d-flex align-items-center position-relative my-1 mx-4">
                            <select name="channel" data-placeholder="Select a Channel..."
                                class="w-250px form-select form-select-solid fw-bolder rounded-sm" id="channelId">
                                <option value="">Select a Channel...</option>
                                <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->channel); ?>"><?php echo e($item->channel); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                    
                    <a href="<?php echo e(route('reconcile.downloadunmatch')); ?>" class="btn btn-sm btn-light-warning me-3 rounded-sm" 
                    
                    >Download</a>
    
                    <!--end::Filter-->
                </div>
                <!--end::Toolbar-->
    
            </div>
            <!--end::Wrapper-->
    
            <!--begin::Datatable-->
    
            <table id="kt_datatable_example_99" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                        
                        
                        
                        <th>MID / MRC</th>
                        
                        <th>Settlement Date</th>
                        <th>Bank Code</th>
                        <th>Merchant Name</th>
                        <th>Account Number</th>
                        <th>Bank Code</th>
                        <th>Bank Type</th>
                        <th>Merchant Name</th>
                        <th>Transfer Amount</th>
                        <th>Sales Amount</th>
                        <th>Bank Transfer</th>
                        <th>Bank Movement</th>
                        <th>Variance</th>
                        <th>Status</th>
                        
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                </tbody>
            </table>
            <!--end::Datatable-->
        </div>
    </div>
    <?php echo $__env->make('/modules/reconcile/mrc-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('/modules/reconcile/download-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/unmatchlist.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/reconcile/unmatch.blade.php ENDPATH**/ ?>