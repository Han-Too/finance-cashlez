<?php
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
    <?php echo csrf_field(); ?>
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
                
                    <button class="col nav-link py-3" id="nav-report-tab" data-bs-toggle="tab" data-bs-target="#nav-report"
                        type="button" role="tab" aria-controls="nav-report" aria-selected="true">
                        <div class="fw-bold fs-6 text-active-primary">
                            Disbursement List
                        </div>
                    </button>
                
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            
            <?php echo $__env->make('modules.reconcile.list.tabs.tabdetail', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('modules.reconcile.list.tabs.tabunmatch', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            
            
                <?php echo $__env->make('modules.reconcile.list.tabs.tabreport', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            
        </div>
    </div>

    <?php echo $__env->make('modules.reconcile.detail-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <?php echo $__env->make('/modules/reconcile/mrc-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('/modules/reconcile/download-modal', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/reconcilenew.js')); ?>"></script>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/reconcile_manualdetail.js')); ?>"></script>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/reconcile_draftdetail.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/reconcile/list/detail.blade.php ENDPATH**/ ?>