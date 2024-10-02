<?php if (isset($component)) { $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da = $component; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(App\View\Components\AppLayout::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
                <?php if(auth()->user()->can(['view-role'])): ?>
                    <button class="col nav-link py-3 active" id="nav-role-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-role" type="button" role="tab" aria-controls="nav-role"
                        aria-selected="true">
                        <div class="fw-bold fs-6 text-active-primary">
                            Role
                        </div>
                    </button>
                <?php endif; ?>
                <?php if(auth()->user()->can(['view-permission'])): ?>
                    <button class="col nav-link py-3" id="nav-permission-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-permission" type="button" role="tab" aria-controls="nav-permission"
                        aria-selected="true">
                        <div class="fw-bold fs-6 text-active-primary">
                            Permission
                        </div>
                    </button>
                <?php endif; ?>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

            <?php if(auth()->user()->can(['view-role'])): ?>
                <?php echo $__env->make('modules.roles.role.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
            <?php if(auth()->user()->can(['view-permission'])): ?>
                <?php echo $__env->make('modules.roles.permission.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>

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