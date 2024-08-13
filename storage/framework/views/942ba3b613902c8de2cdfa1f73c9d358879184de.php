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

        <!--begin::Search-->
        
        <div class="py-3 py-lg-0 py-xl-0"></div>
        <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-bold mb-8 row">
            <li class="nav-item col-4">
                <a class="nav-link text-active-primary pb-4 active" id="nav-edit-tab" data-bs-toggle="tab"
                    href="#nav-edit">Edit Profile</a>
            </li>
            <li class="nav-item col-4">
                <a class="nav-link text-active-primary pb-4" id="nav-password-tab" data-bs-toggle="tab"
                    href="#nav-password">Change Password</a>
            </li>
            <li class="nav-item col-4">
                <a class="nav-link text-active-primary pb-4" id="nav-delete-tab" data-bs-toggle="tab"
                    href="#nav-delete">Delete Account</a>
            </li>
        </ul>
        
        <!--end::Search-->
        
        
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="nav-edit" role="tabpanel" aria-labelledby="nav-edit-tab"
                tabindex="0">
                <?php echo $__env->make('profile.partials.update-profile-information-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="tab-pane fade show" id="nav-password" role="tabpanel" aria-labelledby="nav-password-tab"
                tabindex="0">
                <?php echo $__env->make('profile.partials.update-password-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            <div class="tab-pane fade show" id="nav-delete" role="tabpanel" aria-labelledby="nav-delete-tab"
                tabindex="0">
                <?php echo $__env->make('profile.partials.delete-user-form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </div>
            
            
            
        </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\finance-server\resources\views/profile/edit.blade.php ENDPATH**/ ?>