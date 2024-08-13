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

    $priv = App\Helpers\Utils::getPrivilege('reconcile');
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
            <!--begin::Wrapper-->
            <div class="d-flex flex-stack mb-5">
                <!--begin::Search-->
                <div class="card-title">
                    <!--begin::Search-->
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
                        <input type="text" data-kt-docs-table-filter="search"
                            class="form-control form-control-solid w-250px ps-14 rounded-sm"
                            placeholder="Search Bank" />
                    </div>
                    <!--end::Search-->
                </div>
                <!--end::Search-->

                <!--begin::Toolbar-->
                <?php if($priv->create): ?>
                    <div class="d-flex justify-content-end" data-kt-docs-table-toolbar="base">
                        <a href="#" class="btn btn-light-primary me-3 rounded-sm" data-bs-toggle="modal"
                            data-bs-target="#kt_modal_reconcile">Reconcile</a>
                        <!--end::Filter-->
                    </div>
                <?php endif; ?>
                <!--end::Toolbar-->

            </div>
            <!--end::Wrapper-->

            <!--begin::Datatable-->
            <table id="kt_datatable_example_2" class="table align-middle table-row-dashed fs-6 gy-5">
                <thead>
                    <tr class="text-start text-gray-400 fw-bolder fs-7 text-uppercase gs-0">
                        <th>No</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Settlement File</th>
                        <th class="">BO Date</th>
                        <th class="">Reconcile Date</th>
                        <th class="text-center">Reconcile</th>
                        <th class="text-center">Status</th>
                        <th class="text-end min-w-100px">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold">
                </tbody>
            </table>
            <!--end::Datatable-->
        </div>
    </div>

    <div class="modal fade" id="kt_modal_reconcile" tabindex="-1" aria-hidden="true">
        <!--begin::Modal dialog-->
        <div class="modal-dialog modal-dialog-centered mw-650px rounded-sm">
            <!--begin::Modal content-->
            <div class="modal-content rounded-sm">
                <!--begin::Modal header-->
                <div class="modal-header pb-0 border-0 justify-content-end">
                    <!--begin::Close-->
                    <div class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal">
                        <!--begin::Svg Icon | path: icons/duotune/arrows/arr061.svg-->
                        <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1"
                                    transform="rotate(-45 6 17.3137)" fill="black" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                    transform="rotate(45 7.41422 6)" fill="black" />
                            </svg>
                        </span>
                        <!--end::Svg Icon-->
                    </div>
                    <!--end::Close-->
                </div>
                <!--begin::Modal header-->
                <!--begin::Modal body-->
                <div class="modal-body scroll-y px-10 px-lg-15 pt-0 pb-15">
                    <!--begin:Form-->
                    <?php if($file->isEmpty()): ?>
                        
                        <div class="mb-13 text-center">
                            <div class="h1 text-danger text-center">
                                Upload File Settlement First!
                            </div>
                        </div>

                        <div class="text-center">
                            <a href="/settlement" class="btn btn-primary rounded-sm">Okay</a>
                        </div>
                        
                    <?php else: ?>
                        <form id="store_reconcile_form" class="form" action="#">
                            <?php echo csrf_field(); ?>
                            <!--begin::Heading-->
                            <div class="mb-13 text-center">
                                <!--begin::Title-->
                                <h1 class="mb-3">Reconcile Data</h1>
                                <!--end::Title-->
                            </div>
                            <!--end::Heading-->

                            <!--begin::Input group-->
                            <div class="d-flex flex-column mb-8 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">Reconcile Name</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Name of the bank"></i>
                                </label>
                                <!--end::Label-->
                                <input type="text" class="form-control" name="name"
                                    value="Reconcile-<?php echo e(date('Y-m-d H:i:s')); ?>">
                            </div>
                            <div class="d-flex flex-column mb-8 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">File Settlement</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Name of the bank"></i>
                                </label>
                                <!--end::Label-->
                                <select name="filesettle" aria-label="Select a Channel" data-control="select1"
                                    data-placeholder="Select a File..." class="form-select form-select-solid fw-bolder">
                                    <option value="">Select a File...</option>
                                    <?php $__currentLoopData = $file; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($item->name); ?>"><?php echo e($item->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="d-flex flex-column mb-8 fv-row">
                                <!--begin::Label-->
                                <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                    <span class="required">Back Office Date</span>
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                        title="Range of Back Office Date"></i>
                                </label>
                                <!--end::Label-->
                                <div class="mb-0 w-100 me-1">
                                    <input class="form-control form-control-solid" placeholder="Pick date rage"
                                        id="kt_daterangepicker_1" name="bo_date" />
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" id="kt_modal_reconcile_submit"
                                    class="btn btn-primary rounded-sm">Reconcile</button>
                            </div>
                            <!--end::Actions-->
                        </form>
                    <?php endif; ?>
                    <!--end:Form-->
                </div>
                <!--end::Modal body-->
            </div>
            <!--end::Modal content-->
        </div>
        <!--end::Modal dialog-->
    </div>


    <?php $__env->startSection('scripts'); ?>
        <script>
            var privCreate = <?php echo $priv->create; ?>;
        </script>
        <script src="<?php echo e(asset('cztemp/assets/custom/js/reconcile_list.js')); ?>"></script>
    <?php $__env->stopSection(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da)): ?>
<?php $component = $__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da; ?>
<?php unset($__componentOriginal8e2ce59650f81721f93fef32250174d77c3531da); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/reconcile/list.blade.php ENDPATH**/ ?>