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
                <h2 class="fw-bolder">Edit Channel Parameter</h2>
            </div>
            <form class="form" action="#" id="update_role_form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="id" value="<?php echo e($data->id); ?>">
                <div class="py-10 px-lg-17">
                    <div class="scroll-y me-n7 pe-7">
                        <!--begin::Input group-->
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Channel Name</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <input type="text" class="form-control form-control-solid" placeholder="Place channels's name"
                                name="name" value="<?php echo e($data->channel); ?>" required />
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Report Partner</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="report_partner" aria-label="Select a Parameter" data-control="select2"
                                data-placeholder="Select a Parameter..." class="form-select form-select-solid fw-bolder">
                                <option value="<?php echo e($data->parameter->report_partner); ?>"><?php echo e($data->parameter->report_partner); ?></option>
                                <?php $__currentLoopData = $params; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">BO Detail Transaction</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="bo_detail_transaction" aria-label="Select a Parameter" data-control="select2"
                                data-placeholder="Select a Parameter..." class="form-select form-select-solid fw-bolder">
                                <option value="<?php echo e($data->parameter->bo_detail_transaction); ?>"><?php echo e($data->parameter->bo_detail_transaction); ?></option>
                                <?php $__currentLoopData = $params; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">BO Summary</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="bo_summary" aria-label="Select a Parameter" data-control="select2"
                                data-placeholder="Select a Parameter..." class="form-select form-select-solid fw-bolder">
                                <option value="<?php echo e($data->parameter->bo_summary); ?>"><?php echo e($data->parameter->bo_summary); ?></option>
                                <?php $__currentLoopData = $params; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <!--end::Input-->
                        </div>
                        <div class="fv-row mb-7">
                            <!--begin::Label-->
                            <label class="required fs-6 fw-bold mb-2">Bank Statement</label>
                            <!--end::Label-->
                            <!--begin::Input-->
                            <select name="bank_statement" aria-label="Select a Parameter" data-control="select2"
                                data-placeholder="Select a Parameter..." class="form-select form-select-solid fw-bolder">
                                <option value="<?php echo e($data->parameter->bank_statement); ?>"><?php echo e($data->parameter->bank_statement); ?></option>
                                <?php $__currentLoopData = $params; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item); ?>"><?php echo e($item); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <!--end::Input-->
                        </div>
                        <!--end::Input group-->
                    </div>
                
                </div>
                <!--end::Modal body-->
                <div class="modal-footer flex-center">
                    <!--begin::Button-->
                    <a href="<?php echo e(url('parameters')); ?>" class="btn btn-light me-3 rounded-sm">Back</a>
                    <!--end::Button-->
                    <!--begin::Button-->
                    <button type="submit" name="button" class="btn btn-primary rounded-sm">Submit</button>
                    <!--end::Button-->
                </div>
            </form>
        </div>
    </div>

    <?php $__env->startSection('scripts'); ?>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/parameter.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/parameters/edit.blade.php ENDPATH**/ ?>