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
            <div class="card-title">
                <h2 class="fw-bolder">Edit user</h2>
            </div>
            <form class="form" action="#" id="add_user_form">
                <?php echo csrf_field(); ?>
                <div class="py-10 px-lg-17">
                    <div class="scroll-y me-n7 pe-7">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Username</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" placeholder="Place your username"
                                name="username" required />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" placeholder="Place your name"
                                name="name" required />
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold mb-2">
                                <span class="required">Email</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Email address must be active"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="email" class="form-control form-control-solid" placeholder="Place your email"
                                name="email" required/>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="d-flex flex-column mb-7 fv-row">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold mb-2">
                                <span class="required">Role</span>
                                <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                                    title="Role of user"></i>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="role_id" aria-label="Select a Role"  required
                                data-placeholder="Select a Role..." class="form-select form-select-solid fw-bolder">
                                <?php $__currentLoopData = $role; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->name); ?>"><?php echo e($item->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->

                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold mb-2">
                                <span class="required">Password</span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="password" class="form-control form-control-solid"
                                placeholder="Place your password" name="password" id="password" required/>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="fs-6 fw-bold mb-2">
                                <span class="required">Password Confirmation</span>
                            </label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="password" class="form-control form-control-solid"
                                placeholder="Place your password confirmarion" name="password_confirmation"
                                id="password_confirmation" required/>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>

                </div>
                <!--end::Modal body-->
                <div class="modal-footer flex-center">
                    <!--begin::Button-->
                    <a href="/users" class="btn btn-light me-3">Back</a>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" id="submitedit" name="button" class="btn btn-primary">Save</button>
                    <!--end::Button-->
                </div>
            </form>
        </div>
    </div>

    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/user.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/users/add.blade.php ENDPATH**/ ?>