<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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

            <?php echo $__env->make('modules.roles.role.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php echo $__env->make('modules.roles.permission.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>
    </div>

    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/permission.js')); ?>"></script>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/role.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/roles/index.blade.php ENDPATH**/ ?>