<div class="modal fade" id="kt_modal_reconcile" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
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
                            Tidak ada file yang bisa di reconcile!
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="/settlement" class="btn btn-primary rounded-sm">Upload File</a>
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
                                <span class="required">Channel</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Name of the channel"></i>
                            </label>
                            <!--end::Label-->
                            <select name="channel" id="channelSearch" aria-label="Select a Channel"
                                data-placeholder="Select a Channel..." class="form-select form-select-solid fw-bolder">
                                <option value="">Select a Channel...</option>
                                <?php $__currentLoopData = $channels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($item->bank_id); ?>"><?php echo e($item->channel); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="d-flex flex-column mb-8 fv-row">
                            <label class="d-flex align-items-center fs-6 fw-bold mb-2">
                                <span class="required">File Settlement</span>
                                <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip"
                                    title="Name of the bank"></i>
                            </label>
                            <select name="filesettle" aria-label="Select a Channel" data-control="select1" id="filesettlement"
                                data-placeholder="Select a File..." class="form-select form-select-solid fw-bolder">
                                
                                
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

<?php /**PATH C:\laragon\www\finance-server\resources\views/modules/reconcile/list/modal_recon.blade.php ENDPATH**/ ?>